<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OderItem;
use App\Models\User;

class SampleDataSeeder extends Seeder
{
    protected string $productPath;
    protected string $categoryPath;
    protected string $brandPath;
    protected string $clientImgPath;

    public function __construct()
    {
        $this->productPath = public_path('uploads/products');
        $this->categoryPath = public_path('uploads/categories');
        $this->brandPath = public_path('uploads/brands');
        $this->clientImgPath = public_path('client/img');
    }

    public function run(): void
    {
        // 1. Tạo thư mục nếu chưa có
        File::ensureDirectoryExists($this->productPath);
        File::ensureDirectoryExists($this->categoryPath);
        File::ensureDirectoryExists($this->brandPath);

        $this->command->info('1. Đang chuẩn bị Danh mục sản phẩm (Categories)...');
        $categories = $this->seedCategories();

        $this->command->info('2. Đang chuẩn bị Thương hiệu (Brands)...');
        $brands = $this->seedBrands();

        $this->command->info('3. Đang tải hình ảnh và khởi tạo Sản phẩm (Products)...');
        $products = $this->seedProducts($categories, $brands);

        $this->command->info('4. Đang tạo Mã giảm giá (Coupons)...');
        $this->seedCoupons();

        $this->command->info('5. Đang tạo Đơn hàng mẫu (Orders & Items)...');
        $this->seedOrders($products);

        // Xóa cache view và danh mục
        Cache::forget('global_categories_view');
        $this->command->info('=> Dữ liệu mẫu đã được khởi tạo thành công 100%!');
    }

    /**
     * Helper lưu trữ ảnh: Tận dụng ảnh local hoặc tải từ URL
     */
    protected function prepareImage(string $targetDir, string $filename, ?string $downloadUrl = null, ?string $fallbackLocal = null): string
    {
        $dest = $targetDir . DIRECTORY_SEPARATOR . $filename;

        // Nếu file đã tồn tại và hợp lệ (> 300 bytes để hỗ trợ cả SVG)
        if (File::exists($dest) && File::size($dest) > 300) {
            return $filename;
        }

        // Ưu tiên 1: Copy từ ảnh local fallback trong chính targetDir hoặc client/img hoặc uploads
        if ($fallbackLocal) {
            $targetSource = $targetDir . DIRECTORY_SEPARATOR . $fallbackLocal;
            if (File::exists($targetSource) && File::size($targetSource) > 300) {
                if ($targetSource !== $dest) {
                    File::copy($targetSource, $dest);
                }
                return $filename;
            }
            $localSource = $this->clientImgPath . DIRECTORY_SEPARATOR . $fallbackLocal;
            if (File::exists($localSource) && File::size($localSource) > 300) {
                File::copy($localSource, $dest);
                return $filename;
            }
            $uploadSource = $this->productPath . DIRECTORY_SEPARATOR . $fallbackLocal;
            if (File::exists($uploadSource) && File::size($uploadSource) > 300) {
                File::copy($uploadSource, $dest);
                return $filename;
            }
        }

        // Ưu tiên 2: Tải ảnh từ CDN/URL
        if ($downloadUrl) {
            try {
                $response = Http::timeout(6)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($downloadUrl);

                if ($response->successful() && strlen($response->body()) > 2000) {
                    File::put($dest, $response->body());
                    return $filename;
                }
            } catch (\Throwable $e) {
                // Bỏ qua lỗi mạng, chuyển sang fallback cuối cùng
            }
        }

        // Fallback cuối cùng: Copy ảnh bất kỳ có sẵn trong client/img
        $anyFallback = $this->clientImgPath . DIRECTORY_SEPARATOR . 'product-1.png';
        if (File::exists($anyFallback)) {
            File::copy($anyFallback, $dest);
        }

        return $filename;
    }

    protected function seedCategories(): array
    {
        $catsData = [
            [
                'name' => 'Điện thoại',
                'description' => 'Điện thoại thông minh chính hãng Apple, Samsung, Xiaomi, OPPO...',
                'image' => 'cat-dienthoai.jpg',
                'url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&auto=format&fit=crop&q=80',
                'fallback' => 'iphone-17.jpg',
            ],
            [
                'name' => 'Laptop & PC',
                'description' => 'Laptop văn phòng, Gaming, MacBook và máy trạm đồ họa chuyên nghiệp.',
                'image' => 'cat-laptop.jpg',
                'url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&auto=format&fit=crop&q=80',
                'fallback' => 'macbook-banner.png',
            ],
            [
                'name' => 'Máy tính bảng',
                'description' => 'iPad, Samsung Galaxy Tab, Xiaomi Pad hỗ trợ học tập và làm việc di động.',
                'image' => 'cat-tablet.jpg',
                'url' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=600&auto=format&fit=crop&q=80',
                'fallback' => 'ipad-pro-m4.jpg',
            ],
            [
                'name' => 'Tai nghe & Âm thanh',
                'description' => 'Tai nghe chống ồn, AirPods, loa Bluetooth Marshall, Sony, Bose âm thanh đỉnh cao.',
                'image' => 'cat-audio.jpg',
                'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80',
                'fallback' => 'Sony-WH-1000XM5.jpg',
            ],
            [
                'name' => 'Đồng hồ thông minh',
                'description' => 'Apple Watch, Galaxy Watch theo dõi sức khỏe, thể thao chuyên nghiệp.',
                'image' => 'cat-watch.jpg',
                'url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80',
                'fallback' => 'apple-watch.jpg',
            ],
            [
                'name' => 'Máy ảnh & Quay phim',
                'description' => 'Máy ảnh Mirrorless Sony, ống kính cao cấp, thiết bị quay dựng vlog.',
                'image' => 'cat-camera.jpg',
                'url' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=80',
                'fallback' => 'Camera-product-1.png',
            ],
            [
                'name' => 'Phụ kiện công nghệ',
                'description' => 'Củ sạc GaN, cáp sạc nhanh, chuột công thái học, bàn phím cơ, màn hình Gaming.',
                'image' => 'cat-accessories.jpg',
                'url' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=600&auto=format&fit=crop&q=80',
                'fallback' => 'man-hinh.jpg',
            ],
        ];

        $categories = [];
        foreach ($catsData as $c) {
            $imgName = $this->prepareImage($this->categoryPath, $c['image'], $c['url'], $c['fallback']);
            $cat = Category::updateOrCreate(
                ['name' => $c['name']],
                [
                    'description' => $c['description'],
                    'ImageURL'    => $imgName,
                ]
            );
            $categories[$c['name']] = $cat;
        }

        return $categories;
    }

    protected function seedBrands(): array
    {
        $brandsData = [
            ['name' => 'Apple', 'logo' => 'apple-logo.png', 'desc' => 'Tập đoàn công nghệ Apple - iPhone, iPad, Mac', 'fallback' => 'apple-logo.png'],
            ['name' => 'Samsung', 'logo' => 'samsung-logo.png', 'desc' => 'Tập đoàn điện tử Samsung toàn cầu', 'fallback' => 'samsung-logo.png'],
            ['name' => 'Sony', 'logo' => 'sony-logo.png', 'desc' => 'Âm thanh và máy ảnh chuyên nghiệp Sony', 'fallback' => 'sony-logo.png'],
            ['name' => 'Dell', 'logo' => 'dell-logo.png', 'desc' => 'Thương hiệu máy tính xách tay hàng đầu thế giới', 'fallback' => 'dell-logo.png'],
            ['name' => 'Asus', 'logo' => 'asus-logo.png', 'desc' => 'Bo mạch chủ và Laptop Gaming ROG/TUF đình đám', 'fallback' => 'asus-logo.png'],
            ['name' => 'Lenovo', 'logo' => 'lenovo-logo.png', 'desc' => 'Laptop Legion, ThinkPad và LOQ Gaming', 'fallback' => 'lenovo-logo.png'],
            ['name' => 'Xiaomi', 'logo' => 'xiaomi-logo.png', 'desc' => 'Công nghệ đột phá với mức giá tối ưu', 'fallback' => 'xiaomi-logo.png'],
            ['name' => 'OPPO', 'logo' => 'oppo-logo.png', 'desc' => 'Chuyên gia nhiếp ảnh chân dung di động', 'fallback' => 'oppo-logo.png'],
            ['name' => 'Bose', 'logo' => 'bose-logo.png', 'desc' => 'Thương hiệu âm thanh cao cấp từ Mỹ', 'fallback' => 'bose-logo.png'],
            ['name' => 'Huawei', 'logo' => 'huawei-logo.png', 'desc' => 'Thiết bị đeo thông minh và công nghệ viễn thông', 'fallback' => 'huawei-logo.png'],
            ['name' => 'Logitech', 'logo' => 'logitech-logo.svg', 'desc' => 'Phụ kiện máy tính, chuột và bàn phím số 1 thế giới', 'fallback' => 'logitech-logo.svg'],
            ['name' => 'Marshall', 'logo' => 'marshall-logo.svg', 'desc' => 'Huyền thoại âm thanh Rock & Roll đến từ Anh Quốc', 'fallback' => 'marshall-logo.svg'],
        ];

        $brands = [];
        foreach ($brandsData as $b) {
            $logoName = $this->prepareImage($this->brandPath, $b['logo'], null, $b['fallback']);
            $brand = Brand::updateOrCreate(
                ['TenThuongHieu' => $b['name']],
                [
                    'MoTa'      => $b['desc'],
                    'Logo'      => $logoName,
                    'TrangThai' => 1,
                ]
            );
            $brands[$b['name']] = $brand;
        }

        return $brands;
    }

    protected function seedProducts(array $categories, array $brands): array
    {
        $productsData = [
            // --- ĐIỆN THOẠI ---
            [
                'name' => 'iPhone 16 Pro Max 256GB Titan Sa Mạc',
                'cat' => 'Điện thoại', 'brand' => 'Apple',
                'price' => 34990000, 'discount' => 5, 'stock' => 35, 'weight' => 227,
                'style' => 'Flagship', 'line' => 'iPhone 16 Series',
                'img' => 'iphone-16-pro-max-desert.jpg',
                'url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763395797_iphone-16-pro-max-titan-sa-mac-1-638638962337813406-750x500.jpg',
                'desc' => "• Màn hình Super Retina XDR 6.9 inch ProMotion 120Hz\n• Chip Apple A18 Pro tiến trình 3nm siêu mạnh\n• Camera Fusion 48MP, Telephoto 5x zoom quang học\n• Nút Điều Khiển Camera (Camera Control) mới\n• Pin sử dụng liên tục lên đến 33 giờ xem video\n• Bảo hành chính hãng 12 tháng Apple VN.",
            ],
            [
                'name' => 'iPhone 15 128GB Pink Thời Thượng',
                'cat' => 'Điện thoại', 'brand' => 'Apple',
                'price' => 19890000, 'discount' => 10, 'stock' => 45, 'weight' => 171,
                'style' => 'Thời trang', 'line' => 'iPhone 15',
                'img' => 'iphone-15-pink.jpg',
                'url' => 'https://images.unsplash.com/photo-1591337676887-a217a6970a8a?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'iphone-17-purple.jpg',
                'desc' => "• Dynamic Island tương tác thông minh\n• Camera chính 48MP độ phân giải siêu cao\n• Chip A16 Bionic hiệu năng mượt mà\n• Cổng sạc USB-C tiện lợi đa năng\n• Mặt lưng kính pha màu bền bỉ chống bám vân tay.",
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra 512GB Xám Titan',
                'cat' => 'Điện thoại', 'brand' => 'Samsung',
                'price' => 29990000, 'discount' => 12, 'stock' => 30, 'weight' => 232,
                'style' => 'Flagship', 'line' => 'Galaxy S24',
                'img' => 'samsung-s24-ultra.jpg',
                'url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763397932_samsung-galaxy-s24-ultra-xam-1-750x500.jpg',
                'desc' => "• Quyền năng Galaxy AI: Khoanh tròn để tìm kiếm, Dịch trực tiếp cuộc gọi\n• Khung viền Titan siêu nhẹ và bền bỉ\n• Camera 200MP bắt trọn chi tiết đêm Nightography\n• Bút S-Pen tích hợp viết vẽ không giới hạn\n• Màn hình phẳng Dynamic AMOLED 2X 2600 nits chống lóa.",
            ],
            [
                'name' => 'Samsung Galaxy Z Fold 6 256GB Phantom Black',
                'cat' => 'Điện thoại', 'brand' => 'Samsung',
                'price' => 41990000, 'discount' => 8, 'stock' => 20, 'weight' => 239,
                'style' => 'Gập cao cấp', 'line' => 'Galaxy Z Fold',
                'img' => 'samsung-z-fold-6.jpg',
                'url' => 'https://images.unsplash.com/photo-1580910051074-3eb694886505?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763397663_galaxy-z-fold-7-den-d0dfad04a12a4232ab2160c3f5192364-master.jpg',
                'desc' => "• Thiết kế gập phẳng mỏng nhẹ nhất từng có trên dòng Z Fold\n• Màn hình lớn 7.6 inch đa nhiệm 3 ứng dụng cùng lúc\n• Trợ lý phiên dịch hai màn hình độc đáo\n• Bản lề FlexHinge Armor Aluminum gia cường kháng nước IP48.",
            ],
            [
                'name' => 'Samsung Galaxy S25 256GB Xanh Bạc Hà',
                'cat' => 'Điện thoại', 'brand' => 'Samsung',
                'price' => 22490000, 'discount' => 6, 'stock' => 40, 'weight' => 167,
                'style' => 'Nhỏ gọn', 'line' => 'Galaxy S25',
                'img' => 'samsung-s25-green.jpg',
                'url' => 'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763397872_samsung-galaxy-s25-green-thumbai-600x600.jpg',
                'desc' => "• Chip Snapdragon thế hệ mới tối ưu tiết kiệm pin vượt bậc\n• Kích thước nhỏ gọn 6.2 inch cầm nắm hoàn hảo\n• Tích hợp đầy đủ tính năng Galaxy AI tiên tiến.",
            ],
            [
                'name' => 'Xiaomi 14T Pro 5G 512GB Titan Black',
                'cat' => 'Điện thoại', 'brand' => 'Xiaomi',
                'price' => 16490000, 'discount' => 15, 'stock' => 50, 'weight' => 209,
                'style' => 'Hiệu năng cao', 'line' => 'Xiaomi 14T',
                'img' => 'xiaomi-14t-pro.jpg',
                'url' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763400118_xiaomi-15t-rose-gold-thumb-600x600.jpg',
                'desc' => "• Hệ thống 3 ống kính Leica Summilux chuyên nghiệp\n• Chip MediaTek Dimensity 9300+ 4nm cực mạnh\n• Sạc siêu nhanh 120W HyperCharge đầy pin trong 19 phút\n• Màn hình 144Hz AI Eye-care 1.5K sắc nét.",
            ],
            [
                'name' => 'OPPO Find X7 Ultra 512GB Dual Periscope',
                'cat' => 'Điện thoại', 'brand' => 'OPPO',
                'price' => 22990000, 'discount' => 10, 'stock' => 25, 'weight' => 221,
                'style' => 'Chụp ảnh', 'line' => 'Find X',
                'img' => 'oppo-find-x7.jpg',
                'url' => 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763396879_oppo-find-x9-series-ra-mat-16-10-2025-5.jpg',
                'desc' => "• Cụm 4 camera 50MP cảm biến 1 inch hợp tác cùng Hasselblad\n• Đột phá 2 camera tiềm vọng quang học 3x và 6x\n• Màn hình 2K ProXDR 120Hz độ sáng 4500 nits.",
            ],
            [
                'name' => 'OPPO Reno12 F 5G 8GB/256GB Cam Hổ Phách',
                'cat' => 'Điện thoại', 'brand' => 'OPPO',
                'price' => 8490000, 'discount' => 12, 'stock' => 60, 'weight' => 187,
                'style' => 'Trẻ trung', 'line' => 'Reno12',
                'img' => 'oppo-reno12-f.jpg',
                'url' => 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763397558_oppo-reno12-f-5g-8gb-256gb-cu-01.jpg',
                'desc' => "• Đèn viền cụm camera Halo Light phát sáng thông minh\n• Tính năng AI Xóa vật thể và AI Tách nền chân thực\n• Kháng nước kháng bụi chuẩn IP64.",
            ],

            // --- LAPTOP & PC ---
            [
                'name' => 'MacBook Pro 14 M3 Pro (18GB RAM / 512GB SSD) Space Black',
                'cat' => 'Laptop & PC', 'brand' => 'Apple',
                'price' => 45990000, 'discount' => 5, 'stock' => 20, 'weight' => 1610,
                'style' => 'Doanh nhân đồ họa', 'line' => 'MacBook Pro',
                'img' => 'macbook-pro-14-m3.jpg',
                'url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'macbook-banner.png',
                'desc' => "• Chip M3 Pro với CPU 11 lõi và GPU 14 lõi hỗ trợ Ray Tracing\n• Màn hình Liquid Retina XDR 14.2 inch độ sáng HDR 1600 nits\n• Cổng HDMI, khe đọc thẻ SDXC, MagSafe 3 và 3 cổng Thunderbolt 4\n• Thời lượng pin kỷ lục lên tới 18 giờ làm việc liên tục.",
            ],
            [
                'name' => 'MacBook Air 13 M2 (8GB RAM / 256GB SSD) Midnight',
                'cat' => 'Laptop & PC', 'brand' => 'Apple',
                'price' => 23990000, 'discount' => 8, 'stock' => 40, 'weight' => 1240,
                'style' => 'Mỏng nhẹ', 'line' => 'MacBook Air',
                'img' => 'macbook-air-m2.jpg',
                'url' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'macbook_air_13_m2.png',
                'desc' => "• Thiết kế nhôm nguyên khối siêu mỏng chỉ 1.13cm\n• Chip Apple M2 xử lý đồ họa, video 4K êm ái không cần quạt tản nhiệt\n• Màn hình Liquid Retina 13.6 inch 500 nits rực rỡ.",
            ],
            [
                'name' => 'Apple Mac Studio M2 Max (32GB RAM / 512GB SSD)',
                'cat' => 'Laptop & PC', 'brand' => 'Apple',
                'price' => 49990000, 'discount' => 5, 'stock' => 15, 'weight' => 2700,
                'style' => 'Máy trạm Studio', 'line' => 'Mac Studio',
                'img' => 'mac-studio.jpg',
                'url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'Mac-studio-banner.png',
                'desc' => "• Sức mạnh khủng khiếp trong kích thước máy bàn tí hon 19.7 cm\n• Chip M2 Max 12 CPU, 30 GPU xử lý hàng chục luồng video 8K ProRes\n• Hệ thống tản nhiệt khí êm ái tối đa.",
            ],
            [
                'name' => 'Dell XPS 15 9530 Core i7-13700H / 16GB / 1TB / RTX 4050',
                'cat' => 'Laptop & PC', 'brand' => 'Dell',
                'price' => 42990000, 'discount' => 7, 'stock' => 18, 'weight' => 1920,
                'style' => 'Cao cấp sáng tạo', 'line' => 'Dell XPS',
                'img' => 'dell-xps-15.jpg',
                'url' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'carousel-1.jpg',
                'desc' => "• Vỏ nhôm CNC phay tinh xảo kết hợp chiếu nghỉ tay sợi carbon\n• Màn hình InfinityEdge tràn viền 4 cạnh 3.5K OLED rực rỡ\n• Card đồ họa rời NVIDIA RTX 4050 6GB tối ưu cho Render 3D và dựng phim.",
            ],
            [
                'name' => 'Dell Inspiron 15 3520 Core i5-1235U / 16GB / 512GB SSD',
                'cat' => 'Laptop & PC', 'brand' => 'Dell',
                'price' => 14990000, 'discount' => 10, 'stock' => 50, 'weight' => 1650,
                'style' => 'Văn phòng sinh viên', 'line' => 'Dell Inspiron',
                'img' => 'dell-inspiron-15.jpg',
                'url' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763401201_lenovo-loq-15iax9e-i5-83lk0079vn-thumb-638828190971476549-600x600.jpg',
                'desc' => "• Màn hình 15.6 inch FHD 120Hz mượt mà bảo vệ mắt ComfortView\n• Bàn phím số Fullsize tiện lợi nhập liệu kế toán, văn phòng\n• Bản lề nâng Ergonomic gõ phím thoải mái.",
            ],
            [
                'name' => 'ASUS ROG Zephyrus G16 OLED Core Ultra 7 / 16GB / 1TB / RTX 4060',
                'cat' => 'Laptop & PC', 'brand' => 'Asus',
                'price' => 46990000, 'discount' => 6, 'stock' => 15, 'weight' => 1850,
                'style' => 'Gaming cao cấp', 'line' => 'ROG Zephyrus',
                'img' => 'asus-rog-zephyrus.jpg',
                'url' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763400663_lenovo-gaming-loq-15arp9-r5-83jc00m3vn-2-638909832684831879-750x500.jpg',
                'desc' => "• Laptop Gaming mỏng nhẹ chỉ 1.49cm với dải đèn LED Slash Lighting độc bản\n• Màn hình ROG Nebula OLED 2.5K 240Hz 0.2ms chuẩn điện ảnh DCI-P3 100%\n• Tản nhiệt kim loại lỏng Conductonaut Extreme mát mẻ.",
            ],
            [
                'name' => 'Lenovo LOQ Gaming 15 R5-8645HS / 16GB / 512GB / RTX 4050',
                'cat' => 'Laptop & PC', 'brand' => 'Lenovo',
                'price' => 20490000, 'discount' => 10, 'stock' => 30, 'weight' => 2380,
                'style' => 'Gaming quốc dân', 'line' => 'Lenovo LOQ',
                'img' => 'lenovo-loq-15.jpg',
                'url' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=800&auto=format&fit=crop&q=80',
                'fallback' => '1763400663_lenovo-gaming-loq-15arp9-r5-83jc00m3vn-2-638909832684831879-750x500.jpg',
                'desc' => "• Chip AMD Ryzen 5 8645HS tích hợp nhân NPU xử lý AI\n• Bàn phím TrueStrike hành trình sâu 1.5mm cực sướng tay\n• Khung máy thiết kế hầm hố chuẩn quân đội MIL-STD-810H.",
            ],

            // --- MÁY TÍNH BẢNG ---
            [
                'name' => 'iPad Pro 11 M4 Ultra Retina XDR 256GB Space Black',
                'cat' => 'Máy tính bảng', 'brand' => 'Apple',
                'price' => 28490000, 'discount' => 5, 'stock' => 25, 'weight' => 444,
                'style' => 'Chuyên nghiệp', 'line' => 'iPad Pro M4',
                'img' => 'ipad-pro-m4.jpg',
                'url' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'ipad-pro-m4.jpg',
                'desc' => "• Thiết kế mỏng không tưởng chỉ 5.3mm - sản phẩm mỏng nhất Apple từng tạo\n• Màn hình Tandem OLED Ultra Retina XDR siêu sáng 1000 nits toàn màn hình\n• Chip Apple M4 thế hệ mới với công cụ Neutral Engine 38 nghìn tỷ phép tính/giây.",
            ],
            [
                'name' => 'iPad Air 6 11 inch M2 128GB Xanh Pastel',
                'cat' => 'Máy tính bảng', 'brand' => 'Apple',
                'price' => 16490000, 'discount' => 7, 'stock' => 35, 'weight' => 462,
                'style' => 'Năng động', 'line' => 'iPad Air',
                'img' => 'ipad-air-m2.jpg',
                'url' => 'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'ipad-air-m2.jpg',
                'desc' => "• Chip M2 mang lại hiệu năng đồ họa nhanh hơn 50% so với thế hệ trước\n• Tương thích Apple Pencil Pro với cử chỉ bóp và phản hồi rung\n• Camera trước đặt ở cạnh ngang tối ưu gọi video họp nhóm.",
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 Ultra 12GB/256GB 5G Kèm Bút S-Pen',
                'cat' => 'Máy tính bảng', 'brand' => 'Samsung',
                'price' => 27990000, 'discount' => 10, 'stock' => 15, 'weight' => 732,
                'style' => 'Màn hình khổng lồ', 'line' => 'Galaxy Tab S9',
                'img' => 'samsung-tab-s9-ultra.jpg',
                'url' => 'https://images.unsplash.com/photo-1589739900243-4b52cd9b104e?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'samsung-tab-s9-ultra.jpg',
                'desc' => "• Màn hình cực đại 14.6 inch Dynamic AMOLED 2X 120Hz thay thế laptop\n• Kháng nước kháng bụi toàn diện IP68 cho cả máy và bút S-Pen\n• Chế độ Samsung DeX biến giao diện thành máy tính để bàn linh hoạt.",
            ],

            // --- TAI NGHE & ÂM THANH ---
            [
                'name' => 'Tai nghe Sony WH-1000XM5 Chống Ồn Cao Cấp',
                'cat' => 'Tai nghe & Âm thanh', 'brand' => 'Sony',
                'price' => 7990000, 'discount' => 12, 'stock' => 40, 'weight' => 250,
                'style' => 'Chống ồn đỉnh cao', 'line' => '1000X Series',
                'img' => 'sony-wh-1000xm5.jpg',
                'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'Sony-WH-1000XM5.jpg',
                'desc' => "• 2 bộ vi xử lý và 8 micro triệt tiêu tiếng ồn môi trường tuyệt đối\n• Trình điều khiển 30mm màng loa sợi carbon cho chất âm Hi-Res Audio tinh khiết\n• Thời lượng pin 30 giờ, sạc nhanh 3 phút nghe được 3 giờ.",
            ],
            [
                'name' => 'Tai nghe Apple AirPods Pro 2 USB-C Hộp Sạc MagSafe',
                'cat' => 'Tai nghe & Âm thanh', 'brand' => 'Apple',
                'price' => 5690000, 'discount' => 10, 'stock' => 60, 'weight' => 61,
                'style' => 'True Wireless', 'line' => 'AirPods',
                'img' => 'airpods-pro-2.jpg',
                'url' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'airpods-pro.jpg',
                'desc' => "• Chip H2 khử tiếng ồn chủ động gấp 2 lần thế hệ đầu\n• Âm thanh thích ứng (Adaptive Audio) tự động chuyển đổi thông minh\n• Hộp sạc tích hợp loa tìm kiếm và chip định vị U1 chính xác.",
            ],
            [
                'name' => 'Tai nghe Bose QuietComfort Ultra Headphones Black',
                'cat' => 'Tai nghe & Âm thanh', 'brand' => 'Bose',
                'price' => 8990000, 'discount' => 8, 'stock' => 25, 'weight' => 252,
                'style' => 'Âm thanh không gian', 'line' => 'QuietComfort',
                'img' => 'bose-qc-ultra.jpg',
                'url' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'bose-qc-ultra.jpg',
                'desc' => "• Công nghệ âm thanh không gian Bose Immersive Audio sống động\n• Đệm tai bọc da protein êm ái hàng đầu thế giới đeo cả ngày không mỏi\n• Tùy chỉnh chế độ chống ồn Quiet Mode và Aware Mode nhanh chóng.",
            ],
            [
                'name' => 'Loa Bluetooth Marshall Stanmore III 80W Black',
                'cat' => 'Tai nghe & Âm thanh', 'brand' => 'Marshall',
                'price' => 8490000, 'discount' => 6, 'stock' => 20, 'weight' => 4250,
                'style' => 'Cổ điển Retro', 'line' => 'Home Speaker',
                'img' => 'marshall-stanmore-3.jpg',
                'url' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'marshall-stanmore-3.jpg',
                'desc' => "• Thiết kế vintage bọc da viền đồng mang tính biểu tượng rock n roll\n• Âm trường Stereo rộng hơn với các củ loa tweeter hướng góc ra ngoài\n• Kết nối Bluetooth 5.2 thế hệ mới cùng cổng AUX và RCA đa dạng.",
            ],
            [
                'name' => 'Loa Di Động Marshall Emberton II Kháng Nước IP67',
                'cat' => 'Tai nghe & Âm thanh', 'brand' => 'Marshall',
                'price' => 3890000, 'discount' => 10, 'stock' => 45, 'weight' => 700,
                'style' => 'Dã ngoại', 'line' => 'Portable',
                'img' => 'marshall-emberton-2.jpg',
                'url' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'marshall-emberton-2.jpg',
                'desc' => "• Âm thanh đa hướng 360 độ True Stereophonic độc quyền\n• Chuẩn kháng nước kháng bụi bền bỉ IP67 mang đi mọi cuộc vui\n• Thời lượng pin lên đến hơn 30 giờ chơi nhạc chỉ với 1 lần sạc.",
            ],

            // --- ĐỒNG HỒ THÔNG MINH ---
            [
                'name' => 'Apple Watch Ultra 2 GPS + Cellular 49mm Vỏ Titan',
                'cat' => 'Đồng hồ thông minh', 'brand' => 'Apple',
                'price' => 20990000, 'discount' => 5, 'stock' => 20, 'weight' => 61,
                'style' => 'Thể thao mạo hiểm', 'line' => 'Apple Watch Ultra',
                'img' => 'apple-watch-ultra-2.jpg',
                'url' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'Apple_watch-1.jpg',
                'desc' => "• Vỏ Titan chuẩn hàng không vũ trụ chống ăn mòn tuyệt hảo\n• Màn hình sáng nhất từ trước tới nay đạt 3000 nits nhìn rõ dưới nắng gắt\n• GPS tần số kép chính xác cao L1 & L5\n• Chuẩn lặn biển EN13319 và chịu độ sâu 100m.",
            ],
            [
                'name' => 'Apple Watch Series 9 GPS 45mm Viền Nhôm Midnight',
                'cat' => 'Đồng hồ thông minh', 'brand' => 'Apple',
                'price' => 9990000, 'discount' => 10, 'stock' => 35, 'weight' => 39,
                'style' => 'Năng động hiện đại', 'line' => 'Apple Watch S9',
                'img' => 'apple-watch-s9.jpg',
                'url' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'apple-watch.jpg',
                'desc' => "• Thao tác chạm hai lần (Double Tap) điều khiển bằng một tay diệu kỳ\n• Chip S9 SiP mạnh mẽ hơn 60% với trợ lý Siri xử lý ngay trên thiết bị\n• Cảm biến đo nồng độ Oxy trong máu SpO2 và điện tâm đồ ECG.",
            ],
            [
                'name' => 'Samsung Galaxy Watch 6 Classic 47mm LTE Viền Xoay',
                'cat' => 'Đồng hồ thông minh', 'brand' => 'Samsung',
                'price' => 7490000, 'discount' => 15, 'stock' => 30, 'weight' => 59,
                'style' => 'Cổ điển lịch lãm', 'line' => 'Galaxy Watch Classic',
                'img' => 'samsung-watch-6.jpg',
                'url' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'samsung-watch-6.jpg',
                'desc' => "• Viền xoay bezel vật lý đặc trưng điều hướng mượt mà đẳng cấp\n• Kính Sapphire chống trầy xước và vỏ thép không gỉ nguyên khối\n• Phân tích thành phần cơ thể BIA và huấn luyện giấc ngủ chuyên sâu.",
            ],
            [
                'name' => 'Huawei Watch GT 4 46mm Dây Da Nâu Sang Trọng',
                'cat' => 'Đồng hồ thông minh', 'brand' => 'Huawei',
                'price' => 4990000, 'discount' => 12, 'stock' => 40, 'weight' => 48,
                'style' => 'Doanh nhân sang trọng', 'line' => 'Watch GT',
                'img' => 'huawei-watch-gt-4.jpg',
                'url' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'huawei-watch-gt-4.jpg',
                'desc' => "• Thiết kế góc cạnh hình bát giác Octagonal tinh xảo\n• Pin siêu khủng lên tới 14 ngày sử dụng chỉ với 1 lần sạc đầy\n• Tương thích tuyệt đối với cả điện thoại iOS và Android.",
            ],

            // --- PHỤ KIỆN & THIẾT BỊ KHÁC ---
            [
                'name' => 'Chuột Không Dây Logitech MX Master 3S Công Thái Học',
                'cat' => 'Phụ kiện công nghệ', 'brand' => 'Logitech',
                'price' => 2290000, 'discount' => 10, 'stock' => 60, 'weight' => 141,
                'style' => 'Công thái học', 'line' => 'MX Master',
                'img' => 'logitech-mx-master-3s.jpg',
                'url' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'logitech-mx-master-3s.jpg',
                'desc' => "• Con lăn điện từ MagSpeed cuộn 1.000 dòng trong 1 giây siêu êm\n• Cảm biến 8.000 DPI di chuột mượt mà trên mọi bề mặt kể cả mặt kính\n• Cú click chuột Quiet Clicks giảm 90% tiếng ồn phòng làm việc.",
            ],
            [
                'name' => 'Bàn Phím Cơ Không Dây Logitech MX Mechanical Mini',
                'cat' => 'Phụ kiện công nghệ', 'brand' => 'Logitech',
                'price' => 2990000, 'discount' => 8, 'stock' => 40, 'weight' => 612,
                'style' => 'Bàn phím cơ', 'line' => 'MX Mechanical',
                'img' => 'logitech-mx-mechanical.jpg',
                'url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'logitech-mx-mechanical.jpg',
                'desc' => "• Switch Low-Profile gõ đầm tay cực nhạy và thanh lịch\n• Đèn nền thông minh tự động bật sáng khi bàn tay tiến lại gần\n• Kết nối cùng lúc 3 thiết bị và chuyển đổi nhanh bằng 1 nút bấm.",
            ],
            [
                'name' => 'Màn Hình Gaming Samsung Odyssey G7 28 inch 4K 144Hz',
                'cat' => 'Phụ kiện công nghệ', 'brand' => 'Samsung',
                'price' => 13990000, 'discount' => 10, 'stock' => 15, 'weight' => 6700,
                'style' => 'Màn hình đồ họa Gaming', 'line' => 'Odyssey',
                'img' => 'samsung-odyssey-g7.jpg',
                'url' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'samsung-odyssey.jpg',
                'desc' => "• Tấm nền IPS độ phân giải 4K UHD 3840x2160 cùng tần số quét 144Hz 1ms\n• Tương thích G-Sync và AMD FreeSync Premium Pro triệt tiêu xé hình\n• Đèn hắt CoreSync đồng bộ ánh sáng theo khung cảnh trong game.",
            ],
            [
                'name' => 'Kính Thực Tế Ảo Apple Vision Pro 256GB',
                'cat' => 'Phụ kiện công nghệ', 'brand' => 'Apple',
                'price' => 88990000, 'discount' => 3, 'stock' => 5, 'weight' => 650,
                'style' => 'Điện toán không gian', 'line' => 'Vision Pro',
                'img' => 'apple-vision-pro.jpg',
                'url' => 'https://images.unsplash.com/photo-1593508512255-86ab42a8e620?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'apple-vision-pro.jpg',
                'desc' => "• Đột phá công nghệ điện toán không gian (Spatial Computing) của thế kỷ\n• Màn hình micro-OLED 23 triệu điểm ảnh sắc nét hơn TV 4K cho từng mắt\n• Điều khiển kỳ diệu hoàn toàn bằng ánh mắt, cử chỉ tay và giọng nói.",
            ],

            // --- MÁY ẢNH & THIẾT BỊ SỐ ---
            [
                'name' => 'Máy Ảnh Sony Alpha A1 Mirrorless Flagship Body',
                'cat' => 'Máy ảnh & Quay phim', 'brand' => 'Sony',
                'price' => 89990000, 'discount' => 5, 'stock' => 8, 'weight' => 737,
                'style' => 'Nhiếp ảnh tối thượng', 'line' => 'Alpha A1',
                'img' => 'sony-alpha-a1.png',
                'url' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'Sony-Alpha-A1-removebg-preview.png',
                'desc' => "• Cảm biến xếp chồng Exmor RS BSI CMOS 50.1MP full-frame\n• Chụp liên tiếp 30 khung hình/giây không chớp đen (Blackout-free)\n• Quay video chuẩn điện ảnh 8K 30fps và 4K 120fps 10-bit 4:2:2.",
            ],
            [
                'name' => 'Máy Ảnh Sony Alpha A7 IV Body (ILCE-7M4)',
                'cat' => 'Máy ảnh & Quay phim', 'brand' => 'Sony',
                'price' => 52990000, 'discount' => 8, 'stock' => 15, 'weight' => 658,
                'style' => 'Bán chạy nhất', 'line' => 'Alpha A7',
                'img' => 'sony-alpha-a7-iv.jpg',
                'url' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=800&auto=format&fit=crop&q=80',
                'fallback' => 'Camera-product-1.png',
                'desc' => "• Cảm biến full-frame chiếu sáng sau Exmor R 33MP thế hệ mới\n• Khả năng lấy nét mắt người, động vật và chim thời gian thực bằng AI\n• Màn hình cảm ứng xoay lật đa góc tiện lợi quay Vlog và tự chụp ảnh.",
            ],
        ];

        $products = [];
        foreach ($productsData as $item) {
            $catId = $categories[$item['cat']]->id ?? 1;
            $brandId = $brands[$item['brand']]->id ?? 1;

            $imageName = $this->prepareImage(
                $this->productPath,
                $item['img'],
                $item['url'] ?? null,
                $item['fallback'] ?? null
            );

            $product = Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id'     => $catId,
                    'id_brand'        => $brandId,
                    'price'           => $item['price'],
                    'discountPercent' => $item['discount'],
                    'stockQuantity'   => $item['stock'],
                    'weight'          => $item['weight'] ?? 200,
                    'style'           => $item['style'] ?? 'Tiêu chuẩn',
                    'line'            => $item['line'] ?? null,
                    'description'     => $item['desc'],
                    'imageURL'        => $imageName,
                    'IsActive'        => 1,
                    'status'          => 1,
                ]
            );

            $products[] = $product;
        }

        return $products;
    }

    protected function seedCoupons(): void
    {
        $coupons = [
            [
                'code'        => 'GIAM50K',
                'type'        => 'fixed',
                'value'       => 50000,
                'quantity'    => 100,
                'expiry_date' => '2027-12-31',
            ],
            [
                'code'        => 'GIAM100K',
                'type'        => 'fixed',
                'value'       => 100000,
                'quantity'    => 100,
                'expiry_date' => '2027-12-31',
            ],
            [
                'code'        => 'GIAM500K',
                'type'        => 'fixed',
                'value'       => 500000,
                'quantity'    => 50,
                'expiry_date' => '2027-12-31',
            ],
            [
                'code'        => 'SALE10',
                'type'        => 'percent',
                'value'       => 10,
                'quantity'    => 200,
                'expiry_date' => '2027-12-31',
            ],
            [
                'code'        => 'VIP20',
                'type'        => 'percent',
                'value'       => 20,
                'quantity'    => 50,
                'expiry_date' => '2027-12-31',
            ],
            [
                'code'        => 'FREESHIP',
                'type'        => 'free_ship',
                'value'       => 50000,
                'quantity'    => 500,
                'expiry_date' => '2027-12-31',
            ],
        ];

        foreach ($coupons as $cp) {
            Coupon::updateOrCreate(['code' => $cp['code']], $cp);
        }
    }

    protected function seedOrders(array $products): void
    {
        if (empty($products)) return;

        $user = User::where('role', 'user')->first() ?? User::first();
        $admin = User::where('role', 'admin')->first();

        $sampleOrders = [
            [
                'user_id'          => $user ? $user->id : null,
                'shipping_name'    => 'Nguyễn Đức Công',
                'shipping_phone'   => '0987654321',
                'shipping_email'   => 'cong@example.com',
                'shipping_address' => '123 Đường Cầu Diễn, Phường Phú Diễn, Quận Bắc Từ Liêm, Hà Nội',
                'latitude'         => 21.05350,
                'longitude'        => 105.75860,
                'to_district_id'   => 1482,
                'to_ward_code'     => '1A0107',
                'payment_method'   => 'COD',
                'status'           => 'completed',
                'shipping_status'  => 'delivered',
                'ghn_order_code'   => 'L8HFL8',
                'shipping_fee'     => 35000,
                'discount_amount'  => 50000,
                'notes'            => 'Giao trong giờ hành chính, gọi trước khi đến.',
                'items'            => [
                    ['prod_idx' => 0, 'qty' => 1], // iPhone 16 Pro Max
                    ['prod_idx' => 19, 'qty' => 1], // AirPods Pro 2
                ]
            ],
            [
                'user_id'          => $user ? $user->id : null,
                'shipping_name'    => 'Trần Thị Mai',
                'shipping_phone'   => '0912345678',
                'shipping_email'   => 'mai.tran@example.com',
                'shipping_address' => '45 Lê Duẩn, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
                'latitude'         => 10.776889,
                'longitude'        => 106.700806,
                'to_district_id'   => 1442,
                'to_ward_code'     => '20101',
                'payment_method'   => 'VNPAY',
                'status'           => 'shipping',
                'shipping_status'  => 'delivering',
                'ghn_order_code'   => 'GHN8888VN',
                'shipping_fee'     => 50000,
                'discount_amount'  => 100000,
                'notes'            => 'Đã thanh toán trước qua VNPAY.',
                'items'            => [
                    ['prod_idx' => 8, 'qty' => 1],  // MacBook Pro 14
                    ['prod_idx' => 28, 'qty' => 1], // Chuột MX Master 3S
                ]
            ],
            [
                'user_id'          => $user ? $user->id : null,
                'shipping_name'    => 'Lê Hoàng Nam',
                'shipping_phone'   => '0933445566',
                'shipping_email'   => 'nam.le@example.com',
                'shipping_address' => '78 Nguyễn Thị Minh Khai, Phường 6, Quận 3, TP. Hồ Chí Minh',
                'latitude'         => 10.779444,
                'longitude'        => 106.691389,
                'to_district_id'   => 1444,
                'to_ward_code'     => '20306',
                'payment_method'   => 'COD',
                'status'           => 'processing',
                'shipping_status'  => 'ready_to_pick',
                'ghn_order_code'   => 'GHN9999VN',
                'shipping_fee'     => 30000,
                'discount_amount'  => 0,
                'notes'            => 'Đóng bọc xốp cẩn thận giúp tôi.',
                'items'            => [
                    ['prod_idx' => 2, 'qty' => 1],  // Galaxy S24 Ultra
                ]
            ],
            [
                'user_id'          => $user ? $user->id : null,
                'shipping_name'    => 'Phạm Văn Hùng',
                'shipping_phone'   => '0977889900',
                'shipping_email'   => 'hung.pham@example.com',
                'shipping_address' => '12 Kim Mã, Phường Kim Mã, Quận Ba Đình, Hà Nội',
                'latitude'         => 21.031111,
                'longitude'        => 105.823611,
                'to_district_id'   => 1484,
                'to_ward_code'     => '1A0302',
                'payment_method'   => 'COD',
                'status'           => 'pending',
                'shipping_status'  => 'pending',
                'ghn_order_code'   => null,
                'shipping_fee'     => 25000,
                'discount_amount'  => 0,
                'notes'            => 'Khách hẹn giao buổi chiều tối.',
                'items'            => [
                    ['prod_idx' => 18, 'qty' => 1], // Sony WH-1000XM5
                    ['prod_idx' => 29, 'qty' => 1], // Logitech MX Mechanical
                ]
            ],
            [
                'user_id'          => $user ? $user->id : null,
                'shipping_name'    => 'Vũ Đình Toàn',
                'shipping_phone'   => '0945678901',
                'shipping_email'   => 'toan.vu@example.com',
                'shipping_address' => '88 Cầu Giấy, Phường Quan Hoa, Quận Cầu Giấy, Hà Nội',
                'latitude'         => 21.035278,
                'longitude'        => 105.795556,
                'to_district_id'   => 1485,
                'to_ward_code'     => '1A0405',
                'payment_method'   => 'COD',
                'status'           => 'cancelled',
                'shipping_status'  => 'cancelled',
                'ghn_order_code'   => null,
                'shipping_fee'     => 25000,
                'discount_amount'  => 0,
                'notes'            => 'Khách hàng đổi ý muốn đổi sang phiên bản màu Titan Sa Mạc | Tự hủy 19/09/2026',
                'items'            => [
                    ['prod_idx' => 1, 'qty' => 1], // iPhone 15 Pink
                ]
            ],
            [
                'user_id'          => $user ? $user->id : null,
                'shipping_name' => 'Hoàng Minh Châu',
                'shipping_phone' => '0966554433',
                'shipping_email' => 'chau.hoang@example.com',
                'shipping_address' => '250 Bạch Mai, Phường Cầu Dền, Quận Hai Bà Trưng, Hà Nội',
                'latitude'         => 21.004444,
                'longitude'        => 105.850556,
                'to_district_id'   => 1486,
                'to_ward_code'     => '1A0508',
                'payment_method'   => 'COD',
                'status'           => 'completed',
                'shipping_status'  => 'delivered',
                'ghn_order_code'   => 'GHN7777VN',
                'shipping_fee'     => 0, // Đơn lớn miễn phí ship
                'discount_amount'  => 500000,
                'notes'            => 'Đã nhận hàng và kiểm tra nguyên seal.',
                'items'            => [
                    ['prod_idx' => 21, 'qty' => 1], // Loa Marshall Stanmore III
                    ['prod_idx' => 24, 'qty' => 1], // Apple Watch Ultra 2
                ]
            ],
        ];

        foreach ($sampleOrders as $ord) {
            $totalAmount = 0;
            $orderItemsToCreate = [];

            foreach ($ord['items'] as $it) {
                if (isset($products[$it['prod_idx']])) {
                    $prod = $products[$it['prod_idx']];
                    $price = (float) $prod->price;
                    if ($prod->discountPercent > 0) {
                        $price = $price * (100 - $prod->discountPercent) / 100;
                    }
                    $sub = $price * $it['qty'];
                    $totalAmount += $sub;

                    $orderItemsToCreate[] = [
                        'product_id'   => $prod->id,
                        'product_name' => $prod->name,
                        'price'        => $price,
                        'quantity'     => $it['qty'],
                    ];
                }
            }

            $finalTotal = $totalAmount + $ord['shipping_fee'] - $ord['discount_amount'];
            if ($finalTotal < 0) $finalTotal = 0;

            $createdOrder = Order::create([
                'user_id'          => $ord['user_id'],
                'shipping_name'    => $ord['shipping_name'],
                'shipping_phone'   => $ord['shipping_phone'],
                'shipping_email'   => $ord['shipping_email'],
                'shipping_address' => $ord['shipping_address'],
                'latitude'         => $ord['latitude'],
                'longitude'        => $ord['longitude'],
                'to_district_id'   => $ord['to_district_id'],
                'to_ward_code'     => $ord['to_ward_code'],
                'payment_method'   => $ord['payment_method'],
                'status'           => $ord['status'],
                'shipping_status'  => $ord['shipping_status'],
                'ghn_order_code'   => $ord['ghn_order_code'],
                'shipping_fee'     => $ord['shipping_fee'],
                'discount_amount'  => $ord['discount_amount'],
                'total_amount'     => $finalTotal,
                'total_price'      => $finalTotal,
                'notes'            => $ord['notes'],
            ]);

            foreach ($orderItemsToCreate as $oi) {
                OderItem::create([
                    'order_id'     => $createdOrder->id,
                    'product_id'   => $oi['product_id'],
                    'product_name' => $oi['product_name'],
                    'price'        => $oi['price'],
                    'quantity'     => $oi['quantity'],
                ]);
            }
        }
    }
}
