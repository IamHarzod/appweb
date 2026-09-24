<?php

return [
    'partner_code' => env('MOMO_PARTNER_CODE', 'MOMO5RG620191128'),
    'access_key'   => env('MOMO_ACCESS_KEY', 'M8B2W1D2691N6522'),
    'secret_key'   => env('MOMO_SECRET_KEY', 'at67NAuRicwqStructureSecretKey'),
    'endpoint'     => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
    'redirect_url' => env('MOMO_REDIRECT_URL', 'http://127.0.0.1:8000/momo-return'),
    'ipn_url'      => env('MOMO_IPN_URL', 'http://127.0.0.1:8000/momo-ipn'),
];
