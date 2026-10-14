<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hạn mức tối đa cho phương thức thanh toán khi nhận hàng (COD)
    |--------------------------------------------------------------------------
    | Đơn hàng có tổng thanh toán vượt quá mức này bắt buộc phải thanh toán
    | trực tuyến qua VNPAY hoặc MoMo. Giá trị tính bằng VNĐ.
    */
    'cod_max_limit' => env('ORDER_MAX_COD_AMOUNT', 300000),
];
