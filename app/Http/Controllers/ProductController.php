<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Category;

use App\Models\Brand;


class ProductController extends Controller
{
    public function show_product()
    {
        $product = Product::get();
        return view("admin.product.show_product")->with("products", $product);
    }

    public function show_create_product()
    {
        $brand = Brand::with([
            'brand:TenThuongHieu',        // chọn đúng cột tên của bạn, ví dụ TenThuongHieu
            'category:name'      // ví dụ TenDanhMuc
        ])->get();
        $category = Category::get();
        return view("admin.product.add_product")->with(compact("brand", "category"));
    }

    public function create_product(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stockQuantity' => 'required|integer',
            'discountPercent' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'IsActive' => 'nullable|in:0,1',
            'style' => 'nullable|string',
            'imageURL' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'id_brand' => 'required|integer',
            'category_id' => 'required|integer',
        ]);

        $filename = null;

        // xử lý file chỉ khi có file upload
        if ($request->hasFile('imageURL') && $request->file('imageURL')->isValid()) {
            $file = $request->file('imageURL');

            $destDir = public_path('uploads/products');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            $origNameNoExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = strtolower($file->getClientOriginalExtension());
            $safeBase = Str::slug($origNameNoExt);
            $filename = time() . '_' . $safeBase . '.' . $ext;

            $file->move($destDir, $filename);
        }


        // tạo product — keys phải khớp với protected $fillable trong Model
        Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stockQuantity' => $validated['stockQuantity'],
            'discountPercent' => $validated['discountPercent'] ?? 0,
            'imageURL' => $filename,              // <-- đồng nhất với model/db
            'status' => $validated['status'],
            'IsActive' => $validated['IsActive'] ?? 1,
            'style' => $validated['style'] ?? null,
            'category_id' => $validated['category_id'],
            'id_brand' => $validated['id_brand'],
        ]);

        return redirect("/show-product")->with('success', 'Thêm sản phẩm thành công!');
    }

    public function destroy($id)
    {
        try {
            $product = Product::where("id", $id)->first();
            if ($product->imageURL) {
                $path = public_path('uploads/products/' . $product->imageURL);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            $product->delete();

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function show_edit($id)
    {
        $product = Product::findOrFail($id);
        $brand = Brand::get();
        $category = Category::get();
        return view('admin.product.edit_product', compact('product', 'brand', 'category'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stockQuantity' => 'required|integer',
            'discountPercent' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'IsActive' => 'nullable|in:0,1',
            'style' => 'nullable|string',
            'imageURL' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'id_brand' => 'required|integer',
            'category_id' => 'required|integer',
        ]);

        if ($request->hasFile('imageURL') && $request->file('imageURL')->isValid()) {
            $file = $request->file('imageURL');
            $destDir = public_path('uploads/products');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if ($product->imageURL) {
                $oldPath = $destDir . DIRECTORY_SEPARATOR . $product->imageURL;
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $origNameNoExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = strtolower($file->getClientOriginalExtension());
            $safeBase = Str::slug($origNameNoExt);
            $filename = time() . '_' . $safeBase . '.' . $ext;
            $file->move($destDir, $filename);
            $product->imageURL = $filename;
        }

        $product->name = $validated['name'];
        $product->description = $validated['description'] ?? null;
        $product->price = $validated['price'];
        $product->stockQuantity = $validated['stockQuantity'];
        $product->discountPercent = isset($validated['discountPercent']) ? (float) $validated['discountPercent'] : 0;
        $product->status = $validated['status'];
        $product->IsActive = $validated['IsActive'] ?? $product->IsActive;
        $product->style = $validated['style'] ?? null;
        $product->category_id = $validated['category_id'];
        $product->id_brand = $validated['id_brand'];

        $product->save();

        return redirect('/show-product')->with('success', 'Cập nhật sản phẩm thành công!');
    }
}
