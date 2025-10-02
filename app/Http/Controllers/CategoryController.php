<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    // Hiển thị danh sách category
    public function show_category()
    {
        $categories = Category::orderBy('id', 'desc')->get();
        return view("admin.category.show_category")->with("categories", $categories);
    }

    // Tạo mới category
    public function create_category(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ImageURL' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $filename = null;

        if ($request->hasFile('ImageURL') && $request->file('ImageURL')->isValid()) {
            $file = $request->file('ImageURL');

            $destDir = public_path('uploads/categories');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            $origNameNoExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = strtolower($file->getClientOriginalExtension());
            $safeBase = Str::slug($origNameNoExt);
            $filename = time() . '_' . $safeBase . '.' . $ext;

            $file->move($destDir, $filename);
        }

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'ImageURL' => $filename,
        ]);

        return redirect("/show-category")->with("success", "Thêm danh mục thành công!");
    }

    // Hiển thị form chỉnh sửa (trang riêng)
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view("admin.category.edit_category", compact("category"));
    }

    // Hiển thị modal edit để nạp động như Brand
    public function show_edit_modal($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit_category_modal', compact('category'));
    }

    // Cập nhật category
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ImageURL' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('ImageURL') && $request->file('ImageURL')->isValid()) {
            $file = $request->file('ImageURL');

            $destDir = public_path('uploads/categories');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            // Xoá ảnh cũ nếu có
            if (!empty($category->ImageURL)) {
                $oldPath = public_path('uploads/categories/' . $category->ImageURL);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $origNameNoExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = strtolower($file->getClientOriginalExtension());
            $safeBase = Str::slug($origNameNoExt);
            $filename = time() . '_' . $safeBase . '.' . $ext;

            $file->move($destDir, $filename);

            $category->ImageURL = $filename;
        }

        $category->name = $validated['name'];
        $category->description = $validated['description'] ?? null;
        $category->save();

        return redirect("/show-category")->with("success", "Cập nhật danh mục thành công!");
    }

    // Xoá category
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Xoá file ảnh (nếu có)
            if (!empty($category->ImageURL)) {
                $path = public_path('uploads/categories/' . $category->ImageURL);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            $category->delete();

            return redirect("/show-category")->with("success", "Xoá danh mục thành công!");
        } catch (\Throwable $e) {
            return redirect("/show-category")->with("error", "Xoá thất bại!");
        }
    }
}
