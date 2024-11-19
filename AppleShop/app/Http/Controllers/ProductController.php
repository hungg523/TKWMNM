<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Constants\Product\ProductConstant;

class ProductController extends Controller
{
    // Get all products
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    // Create a new product
    // Create a new product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            ProductConstant::PRODUCT_NAME => 'required|string|max:255',
            ProductConstant::PRODUCT_DESCRIPTION => 'nullable|string',
            ProductConstant::PRODUCT_PRICE => 'required|numeric',
            ProductConstant::PRODUCT_DISCOUNT => 'nullable|numeric',
            ProductConstant::PRODUCT_QUANTITY => 'required|integer',
            ProductConstant::IS_ACTIVED => 'required|boolean',
            ProductConstant::PRODUCT_DETAIL => 'nullable|string',
            ProductConstant::CATEGORY_ID => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    // Update a specific product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'discount' => 'nullable|numeric',
            'quantity' => 'sometimes|required|integer',
            'is_actived' => 'sometimes|required|boolean',
            'product_detail' => 'nullable|string',
            'category_id' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product->update($request->all());
        return response()->json($product);
    }

    // Delete a specific product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
