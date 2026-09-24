<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VNPayService
{
    protected string $vnp_TmnCode;
    protected string $vnp_HashSecret;
    protected string $vnp_Url;
    protected string $vnp_ReturnUrl;

    public function __construct()
    {
        $this->vnp_TmnCode = config('vnpay.vnp_tmn_code');
        $this->vnp_HashSecret = config('vnpay.vnp_hash_secret');
        $this->vnp_Url = config('vnpay.vnp_url');
        $this->vnp_ReturnUrl = route('vnpay.return');
    }

    /**
     * Create VNPay payment URL for redirection
     *
     * @param \App\Models\Order $order
     * @return string
     */
    public function createPaymentUrl($order): string
    {
        $vnp_TxnRef = $order->id . '_' . time();
        $vnp_OrderInfo = "Thanh toan don hang #" . $order->id . " tai AppWeb";
        $vnp_OrderType = 'other';
        $vnp_Amount = round($order->total_amount) * 100; // VNPay amount in VND x 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();
        if (!$vnp_IpAddr || $vnp_IpAddr === '::1') {
            $vnp_IpAddr = '127.0.0.1';
        }

        $createDate = now('Asia/Ho_Chi_Minh')->format('YmdHis');
        $expireDate = now('Asia/Ho_Chi_Minh')->addMinutes(15)->format('YmdHis');

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => $this->vnp_TmnCode,
            "vnp_Amount"     => $vnp_Amount,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => $createDate,
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $vnp_IpAddr,
            "vnp_Locale"     => $vnp_Locale,
            "vnp_OrderInfo"  => $vnp_OrderInfo,
            "vnp_OrderType"  => $vnp_OrderType,
            "vnp_ReturnUrl"  => $this->vnp_ReturnUrl,
            "vnp_TxnRef"     => $vnp_TxnRef,
            "vnp_ExpireDate" => $expireDate,
        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        Log::info('VNPay Payment URL created for Order #' . $order->id, ['url' => $vnp_Url]);

        return $vnp_Url;
    }

    /**
     * Verify VNPay signature from return/IPN callback
     *
     * @param array $params
     * @return bool
     */
    public function verifySignature(array $params): bool
    {
        $inputData = [];
        foreach ($params as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);

        return hash_equals(strtolower($secureHash), strtolower($vnp_SecureHash));
    }
}
