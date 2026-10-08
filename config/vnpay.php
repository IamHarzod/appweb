<?php

return [
    'vnp_tmn_code'    => env('VNPAY_TMN_CODE', 'MZ2NYKV9'),
    'vnp_hash_secret' => env('VNPAY_HASH_SECRET', 'OWUXFEZYYLLVWFEPAUAQFVAGNATTUTWY'),
    'vnp_url'         => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'vnp_return_url'  => env('VNPAY_RETURN_URL'),
    'vnp_ipn_url'     => env('VNPAY_IPN_URL'),
];
