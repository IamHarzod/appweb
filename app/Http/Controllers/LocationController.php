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
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        // 1. Phân giải tọa độ từ Nominatim + BigDataCloud (chạy song song)
        $geo = $this->fetchReverseGeocode($lat, $lng);
        $addr = $geo['nom_address'] ?? [];
        $displayName = $geo['display_name'] ?? '';
        $bdc = $geo['bdc'] ?? [];

        // Trích xuất số nhà và tên đường
        $road = $addr['road'] ?? $addr['street'] ?? '';
        $houseNumber = $addr['house_number'] ?? '';
        $streetAddress = trim($houseNumber . ' ' . $road);
        if (empty($streetAddress)) {
            $streetAddress = $road;
        }

        // Gom toàn bộ từ khóa địa danh từ cả Nominatim và BigDataCloud
        $allParts = [];
        if (!empty($displayName)) $allParts[] = $displayName;
        foreach ($addr as $v) {
            if (is_string($v)) $allParts[] = $v;
        }
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
        $targetProv = $addr['city'] ?? $addr['province'] ?? $addr['state'] ?? ($bdc['principalSubdivision'] ?? '');
        $targetDist = $addr['city_district'] ?? $addr['county'] ?? $addr['district'] ?? $addr['town'] ?? '';
        $targetWard = $addr['suburb'] ?? $addr['quarter'] ?? $addr['neighbourhood'] ?? $addr['village'] ?? $addr['ward'] ?? ($bdc['locality'] ?? '');

        $normProv = $this->normalizeName($targetProv);
        $normDist = $this->normalizeName($targetDist);
        $normWard = $this->normalizeName($targetWard);

        // 2. So khớp Tỉnh / Thành phố với danh mục GHN
        $provinces = $ghn->getProvinces()['data'] ?? [];
        $matchedProv = null;

        // Ưu tiên 1: So khớp chính xác tuyệt đối
        foreach ($provinces as $p) {
            if ($this->normalizeName($p['ProvinceName']) === $normProv) {
                $matchedProv = $p;
                break;
            }
        }
        // Ưu tiên 2: Tên bắt đầu / tiền tố
        if (!$matchedProv && !empty($normProv)) {
            foreach ($provinces as $p) {
                $pNorm = $this->normalizeName($p['ProvinceName']);
                if (str_starts_with($pNorm, $normProv) || str_starts_with($normProv, $pNorm)) {
                    $matchedProv = $p;
                    break;
                }
            }
        }
        // Ưu tiên 3: Tên tỉnh có trong chuỗi địa chỉ
        if (!$matchedProv) {
            foreach ($provinces as $p) {
                $pNorm = $this->normalizeName($p['ProvinceName']);
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
            $districtsRes = $ghn->getDistricts($matchedProv['ProvinceID']);
            $districts = $districtsRes['data'] ?? [];

            // Ưu tiên 1: Khớp chính xác với targetDist
            if (!empty($normDist)) {
                foreach ($districts as $d) {
                    $dNorm = $this->normalizeName($d['DistrictName']);
                    if ($dNorm === $normDist || 'quan ' . $dNorm === $normDist || 'huyen ' . $dNorm === $normDist) {
                        $matchedDist = $d;
                        break;
                    }
                }
            }

            // Ưu tiên 2: Khớp regex theo từ nguyên trong chuỗi địa chỉ tổng hợp
            if (!$matchedDist) {
                foreach ($districts as $d) {
                    $dNorm = $this->normalizeName($d['DistrictName']);
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

            // Ưu tiên 3: So khớp qua NameExtension của GHN
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

            if ($matchedDist) {
                $wardsRes = $ghn->getWards($matchedDist['DistrictID']);
                $wards = $wardsRes['data'] ?? [];

                // Ưu tiên 1: Khớp chính xác với targetWard
                if (!empty($normWard)) {
                    foreach ($wards as $w) {
                        $wNorm = $this->normalizeName($w['WardName']);
                        if ($wNorm === $normWard || 'phuong ' . $wNorm === $normWard || 'xa ' . $wNorm === $normWard) {
                            $matchedWard = $w;
                            break;
                        }
                    }
                }

                // Ưu tiên 2: Khớp regex trong chuỗi địa chỉ tổng hợp
                if (!$matchedWard) {
                    foreach ($wards as $w) {
                        $wNorm = $this->normalizeName($w['WardName']);
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
                    ->withUserAgent('AppWebDelivery/1.0 (contact@appweb.vn)')
                    ->withHeaders(['Referer' => 'https://appweb.vn'])
                    ->timeout(4)
                    ->get("https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1&accept-language=vi"),
                $pool->as('bdc')
                    ->timeout(4)
                    ->get("https://api.bigdatacloud.net/data/reverse-geocode-client?latitude={$lat}&longitude={$lng}&localityLanguage=vi"),
            ]);

            $nom = (isset($responses['nom']) && $responses['nom'] instanceof \Illuminate\Http\Client\Response && $responses['nom']->successful()) 
                ? ($responses['nom']->json() ?? []) 
                : [];
            $bdc = (isset($responses['bdc']) && $responses['bdc'] instanceof \Illuminate\Http\Client\Response && $responses['bdc']->successful()) 
                ? ($responses['bdc']->json() ?? []) 
                : [];
        } catch (\Throwable $e) {
            $nom = [];
            $bdc = [];
        }

        return [
            'nom_address'  => $nom['address'] ?? [],
            'display_name' => $nom['display_name'] ?? '',
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
