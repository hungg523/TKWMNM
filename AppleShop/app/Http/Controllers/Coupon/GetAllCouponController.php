<?php
namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use App\Constants\Coupon\CouponConstant;
use App\Models\Coupon;
use Illuminate\Http\Response;

class GetAllCouponController extends Controller
{
    // Get all coupons
    public function index()
    {
        try {
            // Retrieve all coupons that meet the conditions
            $coupons = Coupon::where(CouponConstant::COUPON_END_DATE, '>', now())
                ->get();

            if ($coupons->isEmpty()) {
                return response()->json(['error' => 'No coupons found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($coupons, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
