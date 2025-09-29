<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function show_brand()
    {
        $brand = Brand::get();
        return view("admin.brand.show_brand")->with("brands", $brand);
    }

    public function create_brand(Request $request)
    {
        $data = $request->all();

        $file = $request->file('Logo');
        if (!$file || !$file->isValid()) {
            return back()->withErrors(['Logo' => 'File không hợp lệ.']);
        }

        $destDir = public_path('uploads/brands');
        if (!is_dir($destDir)) {
            // tạo thư mục nếu chưa có
            mkdir($destDir, 0755, true);
        }

        // 3) Tạo tên file an toàn
        $origNameNoExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext           = strtolower($file->getClientOriginalExtension());
        $safeBase      = Str::slug($origNameNoExt);              // ví dụ: "logo-apple"
        $filename      = time() . '_' . $safeBase . '.' . $ext;          // ví dụ: "1738320000_logo-apple.png"

        // 4) Di chuyển file bằng hàm PHP (move_uploaded_file thông qua UploadedFile::move)
        //    Nếu bạn muốn dùng thật sự move_uploaded_file:
        //    move_uploaded_file($file->getPathname(), $destDir.DIRECTORY_SEPARATOR.$filename);
        $file->move($destDir, $filename);

        $brand = Brand::create([
            'TenThuongHieu' => $data['TenThuongHieu'],
            'Logo' => $filename,
            'MoTa' => $data['MoTa'],
            'TrangThai' => $data['TrangThai'],
        ]);
        return redirect("/show-brand");
    }
}
