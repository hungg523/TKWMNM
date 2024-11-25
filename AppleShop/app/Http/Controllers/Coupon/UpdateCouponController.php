<?php

namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use App\Constants\Coupon\CouponConstant;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class UpdateCouponController extends Controller
{
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                CouponConstant::COUPON_CODE => 'sometimes|required|string|max:255',
                CouponConstant::COUPON_DESCRIPTION => 'sometimes|nullable|string',
                CouponConstant::DISCOUNT_PERCENT => 'sometimes|required|numeric|between:1,100',
                CouponConstant::IS_ACTIVED => 'sometimes|required|numeric|min:0',
                CouponConstant::TIMES_AVAILABLE => 'sometimes|required|numeric|min:0',
                CouponConstant::COUPON_START_DATE => 'sometimes|required|date',
                CouponConstant::COUPON_END_DATE => 'sometimes|required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Find coupon by ID
            $coupon = Coupon::find($id);
            if (!$coupon) {
                return response()->json(['error' => 'Coupon not found'], Response::HTTP_NOT_FOUND);
            }

            $coupon->{CouponConstant::COUPON_CODE} = $request->input(CouponConstant::COUPON_CODE) ?? $coupon->{CouponConstant::COUPON_CODE};
            $coupon->{CouponConstant::COUPON_DESCRIPTION} = $request->input(CouponConstant::COUPON_DESCRIPTION) ?? $coupon->{CouponConstant::COUPON_DESCRIPTION};
            $coupon->{CouponConstant::DISCOUNT_PERCENT} = $request->input(CouponConstant::DISCOUNT_PERCENT) ?? $coupon->input(CouponConstant::DISCOUNT_PERCENT);
            $coupon->{CouponConstant::IS_ACTIVED} = $request->input(CouponConstant::IS_ACTIVED) ?? $coupon->input(CouponConstant::IS_ACTIVED);
            $coupon->{CouponConstant::TIMES_AVAILABLE} = $request->input(CouponConstant::TIMES_AVAILABLE) ?? $coupon->input(CouponConstant::TIMES_AVAILABLE);
            $coupon->{CouponConstant::COUPON_START_DATE} = $request->input(CouponConstant::COUPON_START_DATE) ?? $coupon->{CouponConstant::COUPON_START_DATE};
            $coupon->{CouponConstant::COUPON_END_DATE} = $request->input(CouponConstant::COUPON_END_DATE) ?? $coupon->{CouponConstant::COUPON_END_DATE};

            $coupon->save();
            DB::commit();

            return response()->json(['message' => 'Coupon updated successfully'], Response::HTTP_OK);
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
