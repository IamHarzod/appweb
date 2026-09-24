<?php

return [
    'token'            => env('GHN_TOKEN', ''),
    'shop_id'          => env('GHN_SHOP_ID', ''),
    'endpoint'         => env('GHN_ENDPOINT', 'https://dev-online-gateway.ghn.vn/shiip/public-api/'),
    'from_district_id' => (int) env('GHN_FROM_DISTRICT_ID', 1442),
    'from_ward_code'   => (string) env('GHN_FROM_WARD_CODE', '20101'),
];
