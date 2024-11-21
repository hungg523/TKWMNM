<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Constants\Product\ProductConstant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CreateProductController extends Controller
{
    // Create a new product
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                ProductConstant::CATEGORY_ID => 'required|integer',
                ProductConstant::PRODUCT_NAME => 'required|string|max:255',
                ProductConstant::PRODUCT_DESCRIPTION => 'nullable|string',
                ProductConstant::PRODUCT_PRICE => 'required|numeric',
                ProductConstant::PRODUCT_DISCOUNT => 'nullable|numeric',
                ProductConstant::PRODUCT_QUANTITY => 'required|integer',
                ProductConstant::IS_ACTIVED => 'required|boolean',
                ProductConstant::PRODUCT_DETAIL => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Create product instance
            $product = new Product();
            $product->category_id = $request->{ProductConstant::CATEGORY_ID};
            $product->product_name = $request->{ProductConstant::PRODUCT_NAME};
            $product->description = $request->{ProductConstant::PRODUCT_DESCRIPTION};
            $product->price = $request->{ProductConstant::PRODUCT_PRICE};
            $product->discount = $request->{ProductConstant::PRODUCT_DISCOUNT};
            $product->quantity = $request->{ProductConstant::PRODUCT_QUANTITY};
            $product->is_actived = $request->{ProductConstant::IS_ACTIVED};
            $product->product_detail = $request->{ProductConstant::PRODUCT_DETAIL};
            // Save product to the database
            $product->save();
            DB::commit();

            return response()->json(['message' => 'Product created successfully'], Response::HTTP_CREATED);
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
