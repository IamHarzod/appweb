<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'tinh_thanh'       => 'required',
            'quan_huyen'       => 'required',
            'phuong_xa'        => 'required',
            'shipping_email'   => 'nullable|email|max:255',
            'payment_method'   => 'nullable|string',
            'ghichu'           => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'shipping_name.required'    => 'Vui lòng nhập họ và tên người nhận.',
            'shipping_phone.required'   => 'Vui lòng nhập số điện thoại người nhận.',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'tinh_thanh.required'       => 'Vui lòng chọn Tỉnh/Thành phố.',
            'quan_huyen.required'       => 'Vui lòng chọn Quận/Huyện.',
            'phuong_xa.required'        => 'Vui lòng chọn Phường/Xã.',
        ];
    }
}
