<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::all();
        return view('admin.coupon.show_coupon', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupon.add_coupon');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required',
            'value' => 'required|numeric',
            'quantity' => 'required|integer',
            'expiry_date' => 'required|date',
        ]);

        Coupon::create($data);

        return redirect()->back()->with('success', 'Thêm mã giảm giá thành công!');
    }

    public function destroy($id)
    {
        Coupon::find($id)->delete();
        return redirect()->back()->with('success', 'Đã xoá mã giảm giá!');
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return redirect()->back()->with('error', 'Không tìm thấy mã giảm giá!');
        }

        // Validate dữ liệu
        $request->validate([
            // unique:coupons,code,'.$id nghĩa là: Code phải duy nhất, NHƯNG bỏ qua dòng có id này
            'code' => 'required|unique:coupons,code,' . $id,
            'value' => 'required|numeric',
            'quantity' => 'required|integer',
            'expiry_date' => 'required|date',
        ]);

        // Cập nhật dữ liệu
        $coupon->update([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'quantity' => $request->quantity,
            'expiry_date' => $request->expiry_date,
            // 'status' => $request->status, (nếu có)
        ]);

        return redirect()->back()->with('success', 'Cập nhật mã giảm giá thành công!');
    }
    public function edit($id)
    {
        // 1. Tìm coupon theo ID
        $coupon = Coupon::find($id);

        // 2. Nếu không thấy thì báo lỗi
        if (!$coupon) {
            return redirect()->back()->with('error', 'Không tìm thấy mã giảm giá!');
        }

        // 3. Trả về view sửa (bạn cần tạo file view này ở bước 3)
        return view('admin.coupon.edit_coupon', compact('coupon'));
    }
}
