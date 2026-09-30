<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::firstOrCreate(
            ['TenThuongHieu' => 'Chính Hãng'],
            ['MoTa' => 'Thương hiệu mặc định', 'TrangThai' => 1]
        );

        $products = [
            ['Quần jean nam', 'Quần', 350000, 100],
            ['Áo thun nữ', 'Áo', 250000, 80],
            ['Phụ kiện thời trang', 'Phụ kiện', 150000, 120],
            ['Áo sơ mi nam', 'Áo', 300000, 50],
            ['Quần short nữ', 'Quần', 280000, 60],
            ['Balo mini', 'Phụ kiện', 400000, 40],
            ['Áo khoác jean', 'Áo', 500000, 30],
            ['Vòng tay đá', 'Phụ kiện', 180000, 70],
            ['Quần tây nam', 'Quần', 320000, 90],
            ['Áo hoodie', 'Áo', 450000, 55],
        ];

        foreach ($products as [$name, $categoryName, $price, $quantity]) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            Product::firstOrCreate(
                ['name' => $name, 'category_id' => $category->id],
                [
                    'price'         => $price,
                    'stockQuantity' => $quantity,
                    'id_brand'      => $brand->id,
                    'status'        => 'Còn hàng',
                    'IsActive'      => 1,
                    'description'   => $name . ' chất lượng cao, thiết kế hiện đại.',
                ]
            );
        }
    }
}
