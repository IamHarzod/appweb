<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GHNService
{
    protected string $token;
    protected string $shopId;
    protected string $endpoint;
    protected int $fromDistrictId;
    protected string $fromWardCode;

    public function __construct()
    {
        $this->token = config('ghn.token', '');
        $this->shopId = (string) config('ghn.shop_id', '');
        $this->endpoint = rtrim(config('ghn.endpoint', 'https://dev-online-gateway.ghn.vn/shiip/public-api/'), '/') . '/';
        $this->fromDistrictId = (int) config('ghn.from_district_id', 1482);
        $this->fromWardCode = (string) config('ghn.from_ward_code', '11007');
    }

    /**
     * Lấy danh sách Tỉnh / Thành phố từ GHN (Có Cache 24h)
     */
    public function getProvinces(): array
    {
        return Cache::remember('ghn_provinces_list_v2', 86400, function () {
            try {
                $response = Http::withHeaders([
                    'Token' => $this->token,
                    'Content-Type' => 'application/json'
                ])->post($this->endpoint . 'master-data/province');

                $result = $response->json();
                if (isset($result['code']) && $result['code'] == 200) {
                    $provinces = $result['data'] ?? [];
                    usort($provinces, function ($a, $b) {
                        return strcmp($a['ProvinceName'], $b['ProvinceName']);
                    });
                    return $provinces;
                }
            } catch (\Exception $e) {
                Log::error('GHN getProvinces Error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Lấy danh sách Quận / Huyện từ GHN (Có Cache 24h)
     */
    public function getDistricts(int $provinceId): array
    {
        return Cache::remember("ghn_districts_{$provinceId}_v2", 86400, function () use ($provinceId) {
            try {
                $response = Http::withHeaders([
                    'Token' => $this->token,
                    'Content-Type' => 'application/json'
                ])->post($this->endpoint . 'master-data/district', [
                    'province_id' => $provinceId
                ]);

                $result = $response->json();
                if (isset($result['code']) && $result['code'] == 200) {
                    $districts = $result['data'] ?? [];
                    usort($districts, function ($a, $b) {
                        return strcmp($a['DistrictName'], $b['DistrictName']);
                    });
                    return $districts;
                }
            } catch (\Exception $e) {
                Log::error('GHN getDistricts Error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Lấy danh sách Phường / Xã từ GHN (Có Cache 24h)
     */
    public function getWards(int $districtId): array
    {
        return Cache::remember("ghn_wards_{$districtId}_v2", 86400, function () use ($districtId) {
            try {
                $response = Http::withHeaders([
                    'Token' => $this->token,
                    'Content-Type' => 'application/json'
                ])->post($this->endpoint . 'master-data/ward?district_id=' . $districtId, [
                    'district_id' => $districtId
                ]);

                $result = $response->json();
                if (isset($result['code']) && $result['code'] == 200) {
                    $wards = $result['data'] ?? [];
                    usort($wards, function ($a, $b) {
                        return strcmp($a['WardName'], $b['WardName']);
                    });
                    return $wards;
                }
            } catch (\Exception $e) {
                Log::error('GHN getWards Error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Tính phí vận chuyển GHN
     * 
     * @param int $toDistrictId
     * @param string $toWardCode
     * @param int $weight Grams (mặc định 1000g = 1kg)
     * @param int $insuranceValue Giá trị đơn hàng để khai giá (VNĐ)
     * @return array ['success' => bool, 'fee' => float, 'message' => string]
     */
    public function calculateFee(int $toDistrictId, string $toWardCode, int $weight = 1000, int $insuranceValue = 0): array
    {
        if (empty($this->token) || empty($this->shopId)) {
            return [
                'success' => false,
                'fee' => 30000,
                'message' => 'Chưa cấu hình GHN Token hoặc Shop ID'
            ];
        }

        try {
            // Thử service_type_id = 2 (Chuẩn thương mại điện tử)
            $payload = [
                'service_type_id'  => 2,
                'from_district_id' => $this->fromDistrictId,
                'from_ward_code'   => $this->fromWardCode,
                'to_district_id'   => $toDistrictId,
                'to_ward_code'     => (string) $toWardCode,
                'height'           => 10,
                'length'           => 10,
                'width'            => 10,
                'weight'           => $weight > 0 ? $weight : 1000,
                'insurance_value'  => min($insuranceValue, 5000000) // Tối đa 5tr nếu test
            ];

            $response = Http::withHeaders([
                'Token'        => $this->token,
                'ShopId'       => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->endpoint . 'v2/shipping-order/fee', $payload);

            $result = $response->json();
            Log::info('GHN Fee API Response:', $result ?? []);

            if (isset($result['code']) && $result['code'] == 200 && isset($result['data']['total'])) {
                return [
                    'success' => true,
                    'fee'     => (float) $result['data']['total'],
                    'message' => 'Lấy phí vận chuyển thành công'
                ];
            }

            // Nếu service_type_id = 2 không được, tìm service_id khả dụng
            $serviceResponse = Http::withHeaders([
                'Token'        => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->endpoint . 'v2/shipping-order/available-services', [
                'shop_id'       => (int) $this->shopId,
                'from_district' => $this->fromDistrictId,
                'to_district'   => $toDistrictId
            ]);

            $serviceResult = $serviceResponse->json();
            $serviceId = $serviceResult['data'][0]['service_id'] ?? null;

            if ($serviceId) {
                unset($payload['service_type_id']);
                $payload['service_id'] = $serviceId;

                $response2 = Http::withHeaders([
                    'Token'        => $this->token,
                    'ShopId'       => $this->shopId,
                    'Content-Type' => 'application/json'
                ])->post($this->endpoint . 'v2/shipping-order/fee', $payload);

                $result2 = $response2->json();
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
        } catch (\Exception $e) {
            Log::error('GHN calculateFee Error: ' . $e->getMessage());
            return [
                'success' => false,
                'fee'     => 30000,
                'message' => 'Lỗi kết nối GHN API: ' . $e->getMessage()
            ];
        }
    }
}
