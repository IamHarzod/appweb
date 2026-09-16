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
        $validated = $request->validate([
            'TenThuongHieu' => 'required|string|max:255',
            'Logo' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:4096',
            'MoTa' => 'nullable|string',
            'TrangThai' => 'required|in:0,1',
        ]);

        $file = $request->file('Logo');
        $destDir = public_path('uploads/brands');
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $origNameNoExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext           = strtolower($file->getClientOriginalExtension());
        $safeBase      = Str::slug($origNameNoExt);
        $filename      = time() . '_' . $safeBase . '.' . $ext;

        $file->move($destDir, $filename);

        Brand::create([
            'TenThuongHieu' => $validated['TenThuongHieu'],
            'Logo' => $filename,
            'MoTa' => $validated['MoTa'] ?? null,
            'TrangThai' => $validated['TrangThai'],
        ]);
        return redirect("/show-brand")->with('success', 'Thêm thương hiệu thành công!');
    }

    // Hiển thị form tạo mới thương hiệu (Modal)
    public function showCreate()
    {
        return view('admin.brand.add_brand');
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
            $validated = $request->validate([
                'id' => 'required|exists:brand,id',
                'TenThuongHieu' => 'required|string|max:255',
                'Logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:4096',
                'MoTa' => 'nullable|string',
                'TrangThai' => 'required|in:0,1',
            ]);

            $brand = Brand::findOrFail($validated['id']);

            if ($request->hasFile('Logo') && $request->file('Logo')->isValid()) {
                $file = $request->file('Logo');
                $destDir = public_path('uploads/brands');
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }

                if ($brand->Logo && File::exists($destDir . DIRECTORY_SEPARATOR . $brand->Logo)) {
                    File::delete($destDir . DIRECTORY_SEPARATOR . $brand->Logo);
                }

                $origNameNoExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext           = strtolower($file->getClientOriginalExtension());
                $safeBase      = Str::slug($origNameNoExt);
                $filename      = time() . '_' . $safeBase . '.' . $ext;

                $file->move($destDir, $filename);
                $brand->Logo = $filename;
            }

            $brand->TenThuongHieu = $validated['TenThuongHieu'];
            $brand->MoTa = $validated['MoTa'] ?? null;
            $brand->TrangThai = $validated['TrangThai'];

            $brand->save();
            return redirect("/show-brand")->with('success', 'Cập nhật thương hiệu thành công!');
        } catch (Exception $e) {
            report($e);
            return redirect()->back()->with('error', 'Lỗi khi cập nhật thương hiệu: ' . $e->getMessage());
        }
    }
}
