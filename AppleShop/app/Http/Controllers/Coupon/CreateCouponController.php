<?php

namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Constants\Coupon\CouponConstant;

class CreateCouponController extends Controller
{
    // Create a new coupon
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                CouponConstant::COUPON_CODE => 'required|string|max:255',
                CouponConstant::COUPON_DESCRIPTION => 'nullable|string',
                CouponConstant::DISCOUNT_PERCENT => 'required|numeric|between:1,100',
                CouponConstant::IS_ACTIVED => 'required|numeric|min:0',
                CouponConstant::TIMES_AVAILABLE => 'required|numeric|min:0',
                CouponConstant::COUPON_START_DATE => 'required|date',
                CouponConstant::COUPON_END_DATE => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Create coupon instance
            $coupon = new Coupon();
            $coupon->{CouponConstant::COUPON_CODE} = strtoupper($request->input(CouponConstant::COUPON_CODE));
            $coupon->{CouponConstant::COUPON_DESCRIPTION} = $request->input(CouponConstant::COUPON_DESCRIPTION);
            $coupon->{CouponConstant::DISCOUNT_PERCENT} = $request->input(CouponConstant::DISCOUNT_PERCENT);
            $coupon->{CouponConstant::IS_ACTIVED} = $request->input(CouponConstant::IS_ACTIVED);
            $coupon->{CouponConstant::TIMES_AVAILABLE} = $request->input(CouponConstant::TIMES_AVAILABLE);
            $coupon->{CouponConstant::COUPON_START_DATE} = $request->input(CouponConstant::COUPON_START_DATE);
            $coupon->{CouponConstant::COUPON_END_DATE} = $request->input(CouponConstant::COUPON_END_DATE);

            // Save coupon to the database
            $coupon->save();
            DB::commit();

            return response()->json(['message' => 'Coupon created successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
