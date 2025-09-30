<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
public function show_product()
{
    $product = Product::get();
    return view("admin.product.show_product")->with("products", $product);
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
        // 'category_id' => $validated['category_id'] ?? null,
    ]);

    return redirect("/show-product")->with('success', 'Thêm sản phẩm thành công!');
}
}