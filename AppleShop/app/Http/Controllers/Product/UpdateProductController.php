<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;

use App\Constants\Product\ProductConstant;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class UpdateProductController extends Controller
{
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                ProductConstant::PRODUCT_NAME => 'sometimes|required|string|max:255',
                ProductConstant::PRODUCT_DESCRIPTION => 'nullable|string',
                ProductConstant::PRODUCT_PRICE => 'sometimes|required|numeric',
                ProductConstant::PRODUCT_DISCOUNT => 'nullable|numeric',
                ProductConstant::PRODUCT_QUANTITY => 'sometimes|required|integer',
                ProductConstant::IS_ACTIVED => 'sometimes|required|boolean',
                ProductConstant::PRODUCT_DETAIL => 'nullable|string',
                ProductConstant::CATEGORY_ID => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Find product by ID
            $product = Product::find($id);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }

            // Update product fields
            $product->product_name = $request->{ProductConstant::PRODUCT_NAME} ?? $product->product_name;
            $product->description = $request->{ProductConstant::PRODUCT_DESCRIPTION} ?? $product->description;
            $product->price = $request->{ProductConstant::PRODUCT_PRICE} ?? $product->price;
            $product->discount = $request->{ProductConstant::PRODUCT_DISCOUNT} ?? $product->discount;
            $product->quantity = $request->{ProductConstant::PRODUCT_QUANTITY} ?? $product->quantity;
            $product->is_actived = $request->{ProductConstant::IS_ACTIVED} ?? $product->is_actived;
            $product->product_detail = $request->{ProductConstant::PRODUCT_DETAIL} ?? $product->product_detail;

            // Save product to the database
            $product->save();

            if ($request->has(ProductConstant::CATEGORY_ID)) {
                // Handle product categories
                ProductCategory::where('product_id', $product->id)->delete();
                $categoryIds = collect($request->{ProductConstant::CATEGORY_ID})->unique();
                foreach ($categoryIds as $categoryId) {
                    ProductCategory::create([
                        'product_id' => $product->id,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Product updated successfully'], Response::HTTP_OK);
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
