<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GHNService
{
    protected string $baseUrl;
    protected string $token;
    protected int $shopId;
    protected int $fromDistrictId;
    protected string $fromWardCode;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ghn.base_url', config('ghn.endpoint', 'https://dev-online-gateway.ghn.vn/shiip/public-api')), '/');
        $this->token = (string) (config('services.ghn.token') ?? config('ghn.token', ''));
        $this->shopId = (int) (config('services.ghn.shop_id') ?? config('ghn.shop_id', 0));
        $this->fromDistrictId = (int) (config('services.ghn.from_district_id') ?? config('ghn.from_district_id', 1482));
        $this->fromWardCode = (string) (config('services.ghn.from_ward_code') ?? config('ghn.from_ward_code', '11007'));
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withOptions([
                'verify' => filter_var(config('services.ghn.verify_ssl', false), FILTER_VALIDATE_BOOLEAN),
            ])
            ->acceptJson()
            ->timeout(15)
            ->withHeaders([
                'Token'        => $this->token,
                'ShopId'       => $this->shopId,
                'Content-Type' => 'application/json',
            ]);
    }

    /**
     * Lấy danh sách Tỉnh / Thành phố từ GHN (Có Cache 24h)
     */
    public function getProvinces(): array
    {
        return Cache::remember('ghn_provinces_list_v2', 86400, function () {
            $res = $this->get('/master-data/province');
            if (isset($res['code']) && $res['code'] == 200 && is_array($res['data'] ?? null)) {
                $provinces = $res['data'];
                usort($provinces, function ($a, $b) {
                    return strcmp($a['ProvinceName'] ?? '', $b['ProvinceName'] ?? '');
                });
                return $provinces;
            }
            return $res['data'] ?? [];
        });
    }

    /**
     * Lấy danh sách Quận / Huyện từ GHN (Có Cache 24h)
     */
    public function getDistricts(int $provinceId): array
    {
        return Cache::remember("ghn_districts_{$provinceId}_v2", 86400, function () use ($provinceId) {
            $res = $this->post('/master-data/district', [
                'province_id' => $provinceId
            ]);
            if (isset($res['code']) && $res['code'] == 200 && is_array($res['data'] ?? null)) {
                $districts = $res['data'];
                usort($districts, function ($a, $b) {
                    return strcmp($a['DistrictName'] ?? '', $b['DistrictName'] ?? '');
                });
                return $districts;
            }
            return $res['data'] ?? [];
        });
    }

    /**
     * Lấy danh sách Phường / Xã từ GHN (Có Cache 24h)
     */
    public function getWards(int $districtId): array
    {
        return Cache::remember("ghn_wards_{$districtId}_v2", 86400, function () use ($districtId) {
            $res = $this->post('/master-data/ward?district_id=' . $districtId, [
                'district_id' => $districtId
            ]);
            if (isset($res['code']) && $res['code'] == 200 && is_array($res['data'] ?? null)) {
                $wards = $res['data'];
                usort($wards, function ($a, $b) {
                    return strcmp($a['WardName'] ?? '', $b['WardName'] ?? '');
                });
                return $wards;
            }
            return $res['data'] ?? [];
        });
    }

    /**
     * Tính phí vận chuyển GHN
     */
    public function calculateFee($toDistrictId, $toWardCode = null, int $weight = 1000, int $insuranceValue = 0): array
    {
        if (is_array($toDistrictId)) {
            $params = $toDistrictId;
            return $this->post('/v2/shipping-order/fee', array_merge([
                'shop_id' => $this->shopId,
            ], $params));
        }

        $districtId = (int) $toDistrictId;
        $wardCode = (string) $toWardCode;

        if (empty($this->token) || empty($this->shopId)) {
            return [
                'success' => false,
                'fee' => 30000,
                'message' => 'Chưa cấu hình GHN Token hoặc Shop ID'
            ];
        }

        try {
            $payload = [
                'service_type_id'  => 2,
                'from_district_id' => $this->fromDistrictId,
                'from_ward_code'   => $this->fromWardCode,
                'to_district_id'   => $districtId,
                'to_ward_code'     => $wardCode,
                'height'           => 10,
                'length'           => 10,
                'width'            => 10,
                'weight'           => $weight > 0 ? $weight : 1000,
                'insurance_value'  => min($insuranceValue, 5000000)
            ];

            $result = $this->post('/v2/shipping-order/fee', $payload);

            if (isset($result['code']) && $result['code'] == 200 && isset($result['data']['total'])) {
                return [
                    'success' => true,
                    'fee'     => (float) $result['data']['total'],
                    'message' => 'Lấy phí vận chuyển thành công'
                ];
            }

            // Thử lấy available services nếu service_type_id = 2 không được
            $serviceResult = $this->post('/v2/shipping-order/available-services', [
                'shop_id'       => $this->shopId,
                'from_district' => $this->fromDistrictId,
                'to_district'   => $districtId
            ]);

            $serviceId = $serviceResult['data'][0]['service_id'] ?? null;
            if ($serviceId) {
                unset($payload['service_type_id']);
                $payload['service_id'] = $serviceId;

                $result2 = $this->post('/v2/shipping-order/fee', $payload);
                if (isset($result2['code']) && $result2['code'] == 200 && isset($result2['data']['total'])) {
                    return [
                        'success' => true,
                        'fee'     => (float) $result2['data']['total'],
                        'message' => 'Lấy phí vận chuyển thành công'
                    ];
                }
            }

            return [
                'success' => false,
                'fee'     => 30000,
                'message' => $result['message'] ?? 'Không thể tính phí vận chuyển GHN'
            ];
        } catch (\Throwable $e) {
            Log::error('GHN calculateFee Error: ' . $e->getMessage());
            return [
                'success' => false,
                'fee'     => 30000,
                'message' => 'Lỗi kết nối GHN API: ' . $e->getMessage()
            ];
        }
    }

    // Tạo đơn giao hàng
    public function createOrder(array $orderData): array
    {
        return $this->post('/v2/shipping-order/create', array_merge([
            'shop_id' => $this->shopId,
        ], $orderData));
    }

    // Hủy đơn hàng
    public function cancelOrder(array $orderCodes): array
    {
        return $this->post('/v2/switch-status/cancel', [
            'order_codes' => $orderCodes,
            'shop_id'     => $this->shopId,
        ]);
    }

    protected function get(string $uri, array $query = []): array
    {
        try {
            $response = $this->client()->get($uri, $query);

            if (!$response->successful()) {
                Log::warning('GHN GET request failed', [
                    'uri'    => $uri,
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ]);
                return ['code' => $response->status(), 'message' => 'GHN API request failed.', 'data' => null];
            }

            return $response->json() ?? ['code' => -1, 'message' => 'GHN returned an empty response.', 'data' => null];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => 'Unable to connect to GHN: ' . $exception->getMessage(), 'data' => null];
        } catch (\Throwable $exception) {
            Log::error('GHN GET general error', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => $exception->getMessage(), 'data' => null];
        }
    }

    protected function post(string $uri, array $payload): array
    {
        try {
            $response = $this->client()->post($uri, $payload);

            if (!$response->successful()) {
                Log::warning('GHN POST request failed', [
                    'uri'    => $uri,
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ]);
                return $response->json() ?? ['code' => $response->status(), 'message' => 'GHN API request failed.'];
            }

            return $response->json() ?? ['code' => -1, 'message' => 'GHN returned an empty response.'];
        } catch (ConnectionException $exception) {
            Log::error('Unable to connect to GHN', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => 'Unable to connect to GHN: ' . $exception->getMessage()];
        } catch (\Throwable $exception) {
            Log::error('GHN POST general error', ['uri' => $uri, 'error' => $exception->getMessage()]);
            return ['code' => -1, 'message' => $exception->getMessage()];
        }
    }
}
