<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Constants\Product\ProductConstant;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

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

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            ProductConstant::PRODUCT_NAME => 'sometimes|required|nullable|string|max:255',
            ProductConstant::PRODUCT_DESCRIPTION => 'nullable|string',
            ProductConstant::PRODUCT_PRICE => 'sometimes|required|numeric',
            ProductConstant::PRODUCT_DISCOUNT => 'nullable|numeric',
            ProductConstant::PRODUCT_QUANTITY => 'sometimes|required|integer',
            ProductConstant::IS_ACTIVED => 'sometimes|required|boolean',
            ProductConstant::PRODUCT_DETAIL => 'nullable|string',
            ProductConstant::CATEGORY_ID => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy($product_id)
    {
        $product = Product::find($product_id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
