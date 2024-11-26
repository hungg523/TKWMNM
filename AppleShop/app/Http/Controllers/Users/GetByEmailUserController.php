<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Constants\Users\UserConstant;

class GetByEmailUserController extends Controller
{
    public function getbyemail(Request $request)
    {
        try {
            $request->validate([
                UserConstant::USER_EMAIL => 'required|string|email',
            ]);

            // Find products by name
            $products = Users::where(UserConstant::USER_EMAIL, 'like', '%' . $request->{UserConstant::USER_EMAIL} . '%')->get();
            if ($products->isEmpty()) {
                return response()->json(['error' => ' Email not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($products, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
