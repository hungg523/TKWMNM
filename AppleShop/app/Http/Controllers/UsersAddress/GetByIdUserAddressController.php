<?php

namespace App\Http\Controllers\UsersAddress;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Response;

class GetByIdUserAddressController extends Controller
{
    // Get product detail by ID
    public function show($id)
    {
        try {
            // Find product by ID
            $product = UserAddress::find($id);
            if (!$product) {
                return response()->json(['error' => 'UserId not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($product, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
