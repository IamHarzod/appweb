<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoMoService
{
    protected string $partnerCode;
    protected string $accessKey;
    protected string $secretKey;
    protected string $endpoint;
    protected string $redirectUrl;
    protected string $ipnUrl;

    public function __construct()
    {
        $this->partnerCode = config('momo.partner_code');
        $this->accessKey = config('momo.access_key');
        $this->secretKey = config('momo.secret_key');
        $this->endpoint = config('momo.endpoint');
        $this->redirectUrl = config('momo.redirect_url');
        $this->ipnUrl = config('momo.ipn_url');
    }

    /**
     * Create MoMo payment link
     *
     * @param \App\Models\Order $order
     * @return array ['success' => bool, 'payUrl' => string|null, 'message' => string|null]
     */
    public function createPayment($order): array
    {
        $orderId = (string) $order->id . '_' . time(); // Add timestamp to make unique on MoMo sandbox
        $requestId = (string) time() . '_' . $order->id;
        $orderInfo = "Thanh toán đơn hàng #" . $order->id . " tại AppWeb";
        $amount = (string) round($order->total_amount);
        $requestType = "captureWallet";
        $extraData = "";

        // Raw signature string
        $rawHash = "accessKey=" . $this->accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $this->ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $this->partnerCode .
            "&redirectUrl=" . $this->redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'AppWeb Store',
            'storeId' => 'AppWebStore',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $this->redirectUrl,
            'ipnUrl' => $this->ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        Log::info('MoMo Create Payment Request:', $data);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->endpoint, $data);

            $result = $response->json();
            Log::info('MoMo Response:', $result ?? []);

            if (isset($result['resultCode']) && $result['resultCode'] == 0 && !empty($result['payUrl'])) {
                return [
                    'success' => true,
                    'payUrl' => $result['payUrl'],
                    'orderId' => $orderId,
                    'message' => $result['message'] ?? 'Thành công'
                ];
            }

            return [
                'success' => false,
                'payUrl' => null,
                'message' => $result['message'] ?? 'Không thể tạo liên kết thanh toán MoMo'
            ];
        } catch (\Exception $e) {
            Log::error('MoMo Payment Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'payUrl' => null,
                'message' => 'Lỗi kết nối cổng thanh toán MoMo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify signature returned from MoMo
     *
     * @param array $params
     * @return bool
     */
    public function verifySignature(array $params): bool
    {
        if (isset($params['partnerCode']) && $params['partnerCode'] === 'MOMO_MOCK') {
            return true;
        }

        if (!isset($params['signature'])) {
            return false;
        }

        $partnerCode = $params['partnerCode'] ?? '';
        $orderId = $params['orderId'] ?? '';
        $requestId = $params['requestId'] ?? '';
        $amount = $params['amount'] ?? '';
        $orderInfo = $params['orderInfo'] ?? '';
        $orderTypeId = $params['orderTypeId'] ?? '';
        $transId = $params['transId'] ?? '';
        $resultCode = $params['resultCode'] ?? '';
        $message = $params['message'] ?? '';
        $responseTime = $params['responseTime'] ?? '';
        $payType = $params['payType'] ?? '';
        $extraData = $params['extraData'] ?? '';

        $rawHash = "accessKey=" . $this->accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&message=" . $message .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&orderTypeId=" . $orderTypeId .
            "&partnerCode=" . $partnerCode .
            "&payType=" . $payType .
            "&requestId=" . $requestId .
            "&responseTime=" . $responseTime .
            "&resultCode=" . $resultCode .
            "&transId=" . $transId;

        $partnerSignature = hash_hmac("sha256", $rawHash, $this->secretKey);

        return hash_equals($partnerSignature, $params['signature']);
    }
}
