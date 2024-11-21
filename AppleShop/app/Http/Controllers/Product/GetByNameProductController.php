<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Response;
use App\Constants\Product\ProductConstant;

class GetByNameProductController extends Controller
{
    public function getbyname(Request $request)
    {
        try {
            $request->validate([
                ProductConstant::PRODUCT_NAME => 'required|string',
            ]);

            // Find products by name
            $products = Product::where('product_name', 'like', '%' . $request->{ProductConstant::PRODUCT_NAME} . '%')->get();
            if ($products->isEmpty()) {
                return response()->json(['error' => 'No products found'], Response::HTTP_NOT_FOUND);
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
