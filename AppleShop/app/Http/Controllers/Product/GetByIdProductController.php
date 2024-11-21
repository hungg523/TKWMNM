<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Response;

class GetByIdProductController extends Controller
{
    // Get product detail by ID
    public function show($id)
    {
        try {
            // Find product by ID
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
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
