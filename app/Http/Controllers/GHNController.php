<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GHNService;
use Illuminate\Support\Facades\Session;

class GHNController extends Controller
{
    protected GHNService $ghnService;

    public function __construct(GHNService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    public function getProvinces()
    {
        $provinces = $this->ghnService->getProvinces();
        return response()->json($provinces);
    }

    public function getDistricts($provinceId)
    {
        $districts = $this->ghnService->getDistricts((int) $provinceId);
        return response()->json($districts);
    }

    public function getWards($districtId)
    {
        $wards = $this->ghnService->getWards((int) $districtId);
        return response()->json($wards);
    }

    public function calculateFee(Request $request)
    {
        $request->validate([
            'to_district_id' => 'required|numeric',
            'to_ward_code'   => 'required',
        ]);

        $toDistrictId = (int) $request->input('to_district_id');
        $toWardCode   = (string) $request->input('to_ward_code');
        $subtotal     = (float) $request->input('subtotal', 0);

        $res = $this->ghnService->calculateFee($toDistrictId, $toWardCode, 1000, (int) $subtotal);

        // Lưu phí vận chuyển vừa tính vào Session để Checkout & OrderController cùng dùng
        Session::put('ghn_shipping_fee', $res['fee']);
        Session::put('ghn_to_district_id', $toDistrictId);
        Session::put('ghn_to_ward_code', $toWardCode);

        return response()->json([
            'success'       => $res['success'],
            'fee'           => $res['fee'],
            'fee_formatted' => number_format($res['fee'], 0, ',', '.') . ' VNĐ',
            'message'       => $res['message']
        ]);
    }
}
