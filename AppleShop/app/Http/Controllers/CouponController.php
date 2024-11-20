<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Constants\Coupon\CouponConstant;

class CouponController extends Controller
{
    public function index()
    {
        $coupon = Coupon::all();
        return response()->json($coupon);
    }

    public function show($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        return response()->json($coupon);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            CouponConstant::COUPON_CODE => 'required|string|max:255',
            CouponConstant::COUPON_DESCRIPTION => 'nullable|string',
            CouponConstant::COUPON_START_DATE => 'required|date',
            CouponConstant::COUPON_END_DATE => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $coupon = coupon::create($request->all());
        return response()->json($coupon, 201);
    }

    // Update a specific coupon
    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            CouponConstant::COUPON_CODE => 'required|string|max:255',
            CouponConstant::COUPON_DESCRIPTION => 'nullable|string',
            CouponConstant::COUPON_START_DATE => 'required|date',
            CouponConstant::COUPON_END_DATE => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $coupon->update($request->all());
        return response()->json($coupon);
    }

    // Delete a specific coupon
    public function destroy($coupon_id)
    {
        $coupon = Coupon::find($coupon_id);

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found'], 404);
        }

        $coupon->delete();
        return response()->json(['message' => 'Coupon deleted successfully']);
    }

}