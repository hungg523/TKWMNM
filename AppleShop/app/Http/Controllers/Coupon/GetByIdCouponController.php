<?php
namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Response;

class GetByIdCouponController extends Controller
{
    public function show($id)
    {
        try {
            // Find coupon by ID
            $coupon = Coupon::find($id);
            if (!$coupon) {
                return response()->json(['error' => 'Coupon not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($coupon, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
