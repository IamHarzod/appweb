<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\VNPayService;
use App\Models\Order;

class VNPayServiceTest extends TestCase
{
    public function test_vnpay_service_initialization_and_url_generation()
    {
        $service = new VNPayService();

        $order = new Order();
        $order->id = 999;
        $order->total_amount = 2500000;

        $url = $service->createPaymentUrl($order);

        $this->assertStringContainsString('https://sandbox.vnpayment.vn/paymentv2/vpcpay.html', $url);
        $this->assertStringContainsString('vnp_TmnCode=MZ2NYKV9', $url);
        $this->assertStringContainsString('vnp_Amount=250000000', $url);
        $this->assertStringContainsString('vnp_Command=pay', $url);
        $this->assertStringContainsString('vnp_SecureHash=', $url);
    }

    public function test_vnpay_signature_verification()
    {
        $service = new VNPayService();

        $params = [
            'vnp_Amount' => '250000000',
            'vnp_BankCode' => 'NCB',
            'vnp_BankTranNo' => 'VNP123456',
            'vnp_CardType' => 'ATM',
            'vnp_OrderInfo' => 'Thanh toan don hang #999',
            'vnp_PayDate' => '20261008090000',
            'vnp_ResponseCode' => '00',
            'vnp_TmnCode' => 'MZ2NYKV9',
            'vnp_TransactionNo' => '14000000',
            'vnp_TransactionStatus' => '00',
            'vnp_TxnRef' => '999_123456789',
        ];

        ksort($params);
        $hashData = '';
        $i = 0;
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secret = config('vnpay.vnp_hash_secret');
        $params['vnp_SecureHash'] = hash_hmac('sha512', $hashData, $secret);

        $isValid = $service->verifySignature($params);
        $this->assertTrue($isValid);

        // Invalid hash test
        $params['vnp_SecureHash'] = 'invalid_hash';
        $this->assertFalse($service->verifySignature($params));
    }
}
