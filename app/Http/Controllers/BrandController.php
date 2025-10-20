<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    // Hiển thị thương hiệu
    public function show_brand()
    {
        $brand = Brand::get();
        return view("admin.brand.show_brand")->with("brands", $brand);
    }

    // tạo mới thương hiệu
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

    // Xoá thương hiệu
    public function destroy($id)
    {
        try {
            $brand = Brand::where("id", $id)->first();
            // Xoá file logo (nếu có)
            if ($brand->Logo) {
                $path = public_path('uploads/brands/' . $brand->Logo);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            $brand->delete();

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    // Hiển thị form sửa thương hiệu
    public function showEdit($id)
    {
        $brand = Brand::where('id', $id)->first();
        return view('admin.brand.edit_brand')->with(compact('brand'));
    }

    // Cập nhật dữ liệu về thương hiệu khi sửa
    public function update_brand(Request $request)
    {
        try {
            $brand = Brand::where("id", $request->id)->first();
            if ($brand == null) {
                throw new Exception('Không tìm thấy thương hiệu');
            }

            $file = $request->file('Logo');
            $filename = $file == null ? $brand->Logo : null;
            if ($file != null) {
                if (!$file || !$file->isValid()) {
                    return back()->withErrors(['Logo' => 'File không hợp lệ.']);
                }

                $destDir = public_path('uploads/brands/');
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
                if ($brand->Logo != null) {
                    unlink($destDir . $brand->Logo);
                }
            }

            $brand->TenThuongHieu = $request->TenThuongHieu;
            $brand->Logo = $filename;
            $brand->MoTa = $request->MoTa;
            $brand->TrangThai = $request->TrangThai;

            $brand->save();
            return redirect("/show-brand");
        } catch (Exception $e) {
            report($e);  // ghi log
            throw $e;
        }
    }
}
