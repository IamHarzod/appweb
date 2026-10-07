<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GHNService;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class LocationController extends Controller
{
    public function getProvinces(GHNService $ghn)
    {
        return response()->json($ghn->getProvinces());
    }

    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    public function getWards(int $districtId, GHNService $ghn)
    {
        return response()->json($ghn->getWards($districtId));
    }

    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $request->validate([
            'to_district_id' => 'required',
            'to_ward_code'   => 'required',
        ]);

        // Tính tổng trọng lượng từ giỏ hàng (DB hoặc Session)
        $totalWeight = 0;
        if (Auth::check()) {
            $cart = Cart::with('cartItems.product')->where('user_id', Auth::id())->first();
            if ($cart && $cart->cartItems) {
                foreach ($cart->cartItems as $item) {
                    $itemWeight = (int) ($item->product->weight ?? 200);
                    $totalWeight += $itemWeight * (int) $item->quantity;
                }
            }
        } else {
            $sessionCart = session('cart', []);
            foreach ($sessionCart as $item) {
                $itemWeight = (int) ($item['weight'] ?? 200);
                $totalWeight += $itemWeight * (int) ($item['quantity'] ?? 1);
            }
        }

        if ($totalWeight <= 0) {
            $totalWeight = 300;
        }

        $res = $ghn->calculateFee([
            'service_type_id'  => 2, // Gói chuẩn E-commerce
            'from_district_id' => (int) config('services.ghn.from_district_id'),
            'to_district_id'   => (int) $request->to_district_id,
            'to_ward_code'     => (string) $request->to_ward_code,
            'weight'           => $totalWeight,
            'length'           => 15,
            'width'            => 15,
            'height'           => 10,
        ]);

        return response()->json($res);
    }

    public function reverseGeocode(Request $request, GHNService $ghn)
    {
        $request->validate([
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'client_hint' => 'nullable|array',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;
        $clientHint = $request->input('client_hint') ?? [];

        // 1. Phân giải tọa độ từ Nominatim + Photon OSM + BigDataCloud (chạy song song)
        $geo = $this->fetchReverseGeocode($lat, $lng);
        $addr = $geo['nom_address'] ?? [];
        $displayName = $geo['display_name'] ?? '';
        $photon = $geo['photon_props'] ?? [];
        $bdc = $geo['bdc'] ?? [];

        // Kết hợp dữ liệu từ client hint (nếu có từ trình duyệt người dùng)
        if (!empty($clientHint['address']) && is_array($clientHint['address'])) {
            $addr = array_merge($clientHint['address'], $addr);
        }
        if (empty($displayName) && !empty($clientHint['display_name'])) {
            $displayName = $clientHint['display_name'];
        }

        // Trích xuất số nhà, tên đường chi tiết và địa điểm
        $houseNumber = $addr['house_number'] ?? $photon['housenumber'] ?? '';
        $road = $addr['road'] ?? $addr['street'] ?? $addr['pedestrian'] ?? $addr['footway'] ?? $addr['path'] ?? ($photon['street'] ?? '');
        $poi = $addr['amenity'] ?? $addr['building'] ?? $addr['shop'] ?? $addr['office'] ?? '';
        $photonName = $photon['name'] ?? '';
        if (empty($poi) && !empty($photonName) && $photonName !== $road && ($photon['type'] ?? '') !== 'street') {
            $poi = $photonName;
        }

        $streetParts = [];
        if (!empty($houseNumber) && !empty($road)) {
            $numStr = (preg_match('/^(số|so|no\.?)\s+/iu', $houseNumber)) ? $houseNumber : ('Số ' . $houseNumber);
            $streetParts[] = $numStr . ', ' . $road;
        } elseif (!empty($houseNumber)) {
            $numStr = (preg_match('/^(số|so|no\.?)\s+/iu', $houseNumber)) ? $houseNumber : ('Số ' . $houseNumber);
            $streetParts[] = $numStr;
        } elseif (!empty($road)) {
            $streetParts[] = $road;
        }

        if (!empty($poi) && !in_array($poi, $streetParts)) {
            if (mb_strlen($poi) <= 2) {
                $poi = 'Tòa ' . $poi;
            }
            $streetParts[] = $poi;
        }

        $streetAddress = implode(', ', array_filter(array_unique($streetParts)));

        if (empty($streetAddress)) {
            $fallback = $addr['neighbourhood'] ?? $addr['quarter'] ?? $photon['locality'] ?? '';
            if (!empty($fallback)) {
                $streetAddress = $fallback;
            } elseif (!empty($displayName)) {
                $parts = explode(',', $displayName);
                $streetAddress = trim($parts[0] ?? '');
            } elseif (!empty($bdc['localityInfo']['informative'])) {
                foreach ($bdc['localityInfo']['informative'] as $inf) {
                    $infName = $inf['name'] ?? '';
                    if (preg_match('/\b(đường|duong|phố|pho|ngõ|ngo|ngách|ngach|hẻm|hem)\b/iu', $infName)) {
                        $streetAddress = $infName;
                        break;
                    }
                }
            }
        }

        // Gom toàn bộ từ khóa địa danh từ Nominatim, Photon, BigDataCloud và client hint
        $allParts = [];
        if (!empty($displayName)) $allParts[] = $displayName;
        foreach ($addr as $v) {
            if (is_string($v)) $allParts[] = $v;
        }
        if (!empty($photon['name'])) $allParts[] = $photon['name'];
        if (!empty($photon['street'])) $allParts[] = $photon['street'];
        if (!empty($photon['district'])) $allParts[] = $photon['district'];
        if (!empty($photon['city'])) $allParts[] = $photon['city'];
        if (!empty($photon['locality'])) $allParts[] = $photon['locality'];
        if (!empty($bdc['city'])) $allParts[] = $bdc['city'];
        if (!empty($bdc['locality'])) $allParts[] = $bdc['locality'];
        if (!empty($bdc['principalSubdivision'])) $allParts[] = $bdc['principalSubdivision'];

        foreach ($bdc['localityInfo']['administrative'] ?? [] as $item) {
            if (!empty($item['name'])) $allParts[] = $item['name'];
        }
        foreach ($bdc['localityInfo']['informative'] ?? [] as $item) {
            if (!empty($item['name'])) $allParts[] = $item['name'];
        }

        $rawAddrStr = $this->normalizeName(implode(' ', array_unique($allParts)));

        // Xác định tên cấp hành chính mục tiêu
        $targetProv = $addr['city'] ?? $addr['province'] ?? $addr['state'] ?? ($photon['city'] ?? ($photon['state'] ?? ($bdc['principalSubdivision'] ?? '')));
        $targetDist = $addr['city_district'] ?? $addr['county'] ?? $addr['district'] ?? $addr['town'] ?? ($photon['district'] ?? '');
        $targetWard = $addr['suburb'] ?? $addr['quarter'] ?? $addr['neighbourhood'] ?? $addr['village'] ?? $addr['ward'] ?? ($photon['locality'] ?? ($bdc['locality'] ?? ''));

        $normProv = $this->normalizeName($targetProv);
        $normDist = $this->normalizeName($targetDist);
        $normWard = $this->normalizeName($targetWard);

        // 2. So khớp Tỉnh / Thành phố với danh mục GHN
        $provincesData = $ghn->getProvinces();
        $provinces = (isset($provincesData['data']) && is_array($provincesData['data'])) 
            ? $provincesData['data'] 
            : (is_array($provincesData) ? $provincesData : []);
        $matchedProv = null;

        // Ưu tiên 1: So khớp chính xác tuyệt đối
        foreach ($provinces as $p) {
            if ($this->normalizeName($p['ProvinceName'] ?? '') === $normProv) {
                $matchedProv = $p;
                break;
            }
        }
        // Ưu tiên 2: Tên bắt đầu / tiền tố
        if (!$matchedProv && !empty($normProv)) {
            foreach ($provinces as $p) {
                $pNorm = $this->normalizeName($p['ProvinceName'] ?? '');
                if (str_starts_with($pNorm, $normProv) || str_starts_with($normProv, $pNorm)) {
                    $matchedProv = $p;
                    break;
                }
            }
        }
        // Ưu tiên 3: Tên tỉnh có trong chuỗi địa chỉ
        if (!$matchedProv) {
            foreach ($provinces as $p) {
                $pNorm = $this->normalizeName($p['ProvinceName'] ?? '');
                if (!empty($pNorm) && str_contains($rawAddrStr, $pNorm)) {
                    $matchedProv = $p;
                    break;
                }
            }
        }

        $districts = [];
        $matchedDist = null;
        $wards = [];
        $matchedWard = null;

        if ($matchedProv) {
            $districtsRes = $ghn->getDistricts((int) $matchedProv['ProvinceID']);
            $districts = (isset($districtsRes['data']) && is_array($districtsRes['data'])) 
                ? $districtsRes['data'] 
                : (is_array($districtsRes) ? $districtsRes : []);

            // Thu thập các ứng viên Phường / Xã (để dùng khi suy luận Quận từ Phường)
            $wardCandidates = array_values(array_filter(array_unique([
                $normWard,
                $normDist, // Photon hoặc OSM đôi khi gán nhầm tên Phường vào trường district
                $this->normalizeName($addr['suburb'] ?? ''),
                $this->normalizeName($addr['quarter'] ?? ''),
                $this->normalizeName($addr['neighbourhood'] ?? ''),
                $this->normalizeName($addr['village'] ?? ''),
                $this->normalizeName($photon['locality'] ?? ''),
                $this->normalizeName($bdc['locality'] ?? ''),
            ])));

            // Ưu tiên 1: Khớp Quận / Huyện chính xác với targetDist
            if (!empty($normDist)) {
                foreach ($districts as $d) {
                    $dNorm = $this->normalizeName($d['DistrictName'] ?? '');
                    if ($dNorm === $normDist || 'quan ' . $dNorm === $normDist || 'huyen ' . $dNorm === $normDist) {
                        $matchedDist = $d;
                        break;
                    }
                }
            }

            // Ưu tiên 2: Khớp Quận / Huyện regex theo từ nguyên trong chuỗi địa chỉ tổng hợp
            if (!$matchedDist) {
                foreach ($districts as $d) {
                    $dNorm = $this->normalizeName($d['DistrictName'] ?? '');
                    if (empty($dNorm)) continue;

                    if (is_numeric($dNorm)) {
                        if (preg_match('/\b(quan|q\.|q|district)\s*' . $dNorm . '\b/iu', $rawAddrStr)) {
                            $matchedDist = $d;
                            break;
                        }
                    } else {
                        if (preg_match('/\b' . preg_quote($dNorm, '/') . '\b/iu', $rawAddrStr)) {
                            $matchedDist = $d;
                            break;
                        }
                    }
                }
            }

            // Ưu tiên 3: So khớp Quận / Huyện qua NameExtension của GHN
            if (!$matchedDist) {
                foreach ($districts as $d) {
                    foreach ($d['NameExtension'] ?? [] as $ext) {
                        $extNorm = $this->normalizeName($ext);
                        if (empty($extNorm) || strlen($extNorm) < 3) continue;
                        if (preg_match('/\b' . preg_quote($extNorm, '/') . '\b/iu', $rawAddrStr)) {
                            $matchedDist = $d;
                            break 2;
                        }
                    }
                }
            }

            // Ưu tiên 4: NẾU CHƯA TÌM THẤY QUẬN -> SUY LUẬN QUẬN DỰA VÀO PHƯỜNG / XÃ TRONG TỈNH
            // (Đặc biệt quan trọng với OpenStreetMap tại Việt Nam khi Nominatim chỉ trả về Phường mà bỏ qua Quận)
            if (!$matchedDist && (!empty($wardCandidates) || !empty($rawAddrStr))) {
                $districtIds = array_column($districts, 'DistrictID');
                $allDistrictWards = $ghn->getMultipleDistrictsWards($districtIds);

                // Lượt 1: So khớp chính xác tên Phường / Xã với wardCandidates
                foreach ($districts as $d) {
                    $did = (int) $d['DistrictID'];
                    $dWards = $allDistrictWards[$did] ?? [];

                    foreach ($dWards as $w) {
                        $wNorm = $this->normalizeName($w['WardName'] ?? '');
                        if (empty($wNorm)) continue;

                        if (in_array($wNorm, $wardCandidates, true) 
                            || in_array('phuong ' . $wNorm, $wardCandidates, true) 
                            || in_array('xa ' . $wNorm, $wardCandidates, true)
                            || in_array('thi tran ' . $wNorm, $wardCandidates, true)) {
                            $matchedDist = $d;
                            $matchedWard = $w;
                            $wards = $dWards;
                            break 2;
                        }
                    }
                }

                // Lượt 2: So khớp tên Phường / Xã nằm trong chuỗi địa chỉ tổng hợp
                if (!$matchedDist) {
                    foreach ($districts as $d) {
                        $did = (int) $d['DistrictID'];
                        $dWards = $allDistrictWards[$did] ?? [];

                        foreach ($dWards as $w) {
                            $wNorm = $this->normalizeName($w['WardName'] ?? '');
                            if (empty($wNorm) || mb_strlen($wNorm) < 3) continue;

                            if (preg_match('/\b' . preg_quote($wNorm, '/') . '\b/iu', $rawAddrStr)) {
                                $matchedDist = $d;
                                $matchedWard = $w;
                                $wards = $dWards;
                                break 2;
                            }

                            foreach ($w['NameExtension'] ?? [] as $ext) {
                                $extNorm = $this->normalizeName($ext);
                                if (empty($extNorm) || mb_strlen($extNorm) < 3) continue;
                                if (preg_match('/\b' . preg_quote($extNorm, '/') . '\b/iu', $rawAddrStr)) {
                                    $matchedDist = $d;
                                    $matchedWard = $w;
                                    $wards = $dWards;
                                    break 3;
                                }
                            }
                        }
                    }
                }
            }

            // Nếu Quận đã khớp nhưng Phường chưa xác định -> Tải Phường của Quận đó để so khớp
            if ($matchedDist && empty($matchedWard)) {
                $wardsRes = $ghn->getWards((int) $matchedDist['DistrictID']);
                $wards = (isset($wardsRes['data']) && is_array($wardsRes['data'])) 
                    ? $wardsRes['data'] 
                    : (is_array($wardsRes) ? $wardsRes : []);

                // Ưu tiên 1: Khớp chính xác với targetWard hoặc wardCandidates
                foreach ($wards as $w) {
                    $wNorm = $this->normalizeName($w['WardName'] ?? '');
                    if (in_array($wNorm, $wardCandidates, true) 
                        || in_array('phuong ' . $wNorm, $wardCandidates, true) 
                        || in_array('xa ' . $wNorm, $wardCandidates, true)) {
                        $matchedWard = $w;
                        break;
                    }
                }

                // Ưu tiên 2: Khớp regex trong chuỗi địa chỉ tổng hợp
                if (!$matchedWard) {
                    foreach ($wards as $w) {
                        $wNorm = $this->normalizeName($w['WardName'] ?? '');
                        if (empty($wNorm)) continue;

                        if (is_numeric($wNorm)) {
                            if (preg_match('/\b(phuong|p\.|p|xa|x)\s*' . $wNorm . '\b/iu', $rawAddrStr)) {
                                $matchedWard = $w;
                                break;
                            }
                        } else {
                            if (preg_match('/\b' . preg_quote($wNorm, '/') . '\b/iu', $rawAddrStr)) {
                                $matchedWard = $w;
                                break;
                            }
                        }
                    }
                }

                // Ưu tiên 3: So khớp qua NameExtension của GHN
                if (!$matchedWard) {
                    foreach ($wards as $w) {
                        foreach ($w['NameExtension'] ?? [] as $ext) {
                            $extNorm = $this->normalizeName($ext);
                            if (empty($extNorm) || strlen($extNorm) < 3) continue;
                            if (preg_match('/\b' . preg_quote($extNorm, '/') . '\b/iu', $rawAddrStr)) {
                                $matchedWard = $w;
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        return response()->json([
            'success'        => true,
            'street_address' => $streetAddress,
            'display_name'   => $displayName,
            'province'       => $matchedProv ? [
                'id'   => $matchedProv['ProvinceID'],
                'name' => $matchedProv['ProvinceName'],
            ] : null,
            'district'       => $matchedDist ? [
                'id'   => $matchedDist['DistrictID'],
                'name' => $matchedDist['DistrictName'],
            ] : null,
            'ward'           => $matchedWard ? [
                'code' => $matchedWard['WardCode'],
                'name' => $matchedWard['WardName'],
            ] : null,
            'districts'      => $districts,
            'wards'          => $wards,
        ]);
    }

    protected function fetchReverseGeocode(float $lat, float $lng): array
    {
        try {
            $responses = \Illuminate\Support\Facades\Http::pool(fn (\Illuminate\Http\Client\Pool $pool) => [
                $pool->as('nom')
                    ->withHeaders([
                        'User-Agent' => 'AppWeb-ShopEcommerce/1.0 (https://localhost/appweb; support@appweb.vn)',
                        'Accept-Language' => 'vi,en;q=0.9',
                    ])
                    ->timeout(6)
                    ->get("https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1&accept-language=vi"),
                $pool->as('photon')
                    ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')
                    ->timeout(6)
                    ->get("https://photon.komoot.io/reverse?lat={$lat}&lon={$lng}"),
                $pool->as('bdc')
                    ->withUserAgent('Mozilla/5.0')
                    ->timeout(6)
                    ->get("https://api.bigdatacloud.net/data/reverse-geocode-client?latitude={$lat}&longitude={$lng}&localityLanguage=vi"),
            ]);

            $nom = (isset($responses['nom']) && $responses['nom'] instanceof \Illuminate\Http\Client\Response && $responses['nom']->successful()) 
                ? ($responses['nom']->json() ?? []) 
                : [];
            $photon = (isset($responses['photon']) && $responses['photon'] instanceof \Illuminate\Http\Client\Response && $responses['photon']->successful()) 
                ? ($responses['photon']->json() ?? []) 
                : [];
            $bdc = (isset($responses['bdc']) && $responses['bdc'] instanceof \Illuminate\Http\Client\Response && $responses['bdc']->successful()) 
                ? ($responses['bdc']->json() ?? []) 
                : [];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Reverse geocode error: ' . $e->getMessage());
            $nom = [];
            $photon = [];
            $bdc = [];
        }

        return [
            'nom_address'  => $nom['address'] ?? [],
            'display_name' => $nom['display_name'] ?? '',
            'photon_props' => $photon['features'][0]['properties'] ?? [],
            'bdc'          => $bdc,
        ];
    }

    protected function normalizeName(?string $str): string
    {
        if (empty($str)) return '';
        $str = mb_strtolower(trim($str), 'UTF-8');
        $prefixes = [
            'thành phố ', 'tỉnh ', 'tp. ', 'tp ', 't. ',
            'quận ', 'huyện ', 'thị xã ', 'q. ', 'h. ', 'tx. ', 'tx ',
            'phường ', 'xã ', 'thị trấn ', 'p. ', 'x. ', 'tt. ', 'tt '
        ];
        foreach ($prefixes as $p) {
            if (str_starts_with($str, $p)) {
                $str = substr($str, strlen($p));
                break;
            }
        }
        $str = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $str);
        $str = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $str);
        $str = preg_replace('/[íìỉĩị]/u', 'i', $str);
        $str = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $str);
        $str = preg_replace('/[úùủũụưứừửữự]/u', 'u', $str);
        $str = preg_replace('/[ýỳỷỹỵ]/u', 'y', $str);
        $str = preg_replace('/[đ]/u', 'd', $str);
        return trim(preg_replace('/\s+/', ' ', $str));
    }
}
