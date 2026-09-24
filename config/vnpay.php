<?php

return [
    'vnp_tmn_code'    => env('VNPAY_TMN_CODE', 'CGXZ858Z'),
    'vnp_hash_secret' => env('VNPAY_HASH_SECRET', 'XBAAOWEZYKXNDMHIZBAKDNDUHKYBEYMV'),
    'vnp_url'         => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'vnp_return_url'  => env('VNPAY_RETURN_URL', 'http://127.0.0.1:8000/vnpay-return'),
    'vnp_ipn_url'     => env('VNPAY_IPN_URL', 'http://127.0.0.1:8000/vnpay-ipn'),
];
