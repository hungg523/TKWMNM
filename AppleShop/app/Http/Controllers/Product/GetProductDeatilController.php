<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Constants\Order\OrderConstant;
use App\Constants\Product\ProductConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GetProductDeatilController extends Controller
{
    public function getProductDetail(Request $request)
    {
        // Validation request data
        $validator = Validator::make($request->all(), [
            ProductConstant::PRODUCT_ID => 'integer',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 400);
        }

        $product = Product::find($request->input(ProductConstant::PRODUCT_ID));

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }

        $successfulOrders = Order::where(OrderConstant::STATUS, 'Successed')->get();
        $successfulOrderIds = $successfulOrders->pluck(OrderConstant::ORDER_ID)->toArray();

        $orderItems = OrderItem::whereIn(OrderConstant::ORDER_ID, $successfulOrderIds)->get();

        $productSaleCount = $orderItems->groupBy(ProductConstant::PRODUCT_ID)->map(function ($group) {
            return $group->sum(ProductConstant::PRODUCT_QUANTITY);
        });

        $productDto = new \stdClass();
        $productDto->{ProductConstant::PRODUCT_ID} = $product->{ProductConstant::PRODUCT_ID};
        $productDto->{ProductConstant::PRODUCT_NAME} = $product->{ProductConstant::PRODUCT_NAME};
        $productDto->{ProductConstant::PRODUCT_PRICE} = $product->{ProductConstant::PRODUCT_PRICE};
        $productDto->{ProductConstant::PRODUCT_DISCOUNT} = $product->{ProductConstant::PRODUCT_DISCOUNT};
        $productDto->{ProductConstant::PRODUCT_QUANTITY} = $product->{ProductConstant::PRODUCT_QUANTITY};
        $productDto->{ProductConstant::PRODUCT_DESCRIPTION} = $product->{ProductConstant::PRODUCT_DESCRIPTION};
        $productDto->{ProductConstant::IMG_URL} = $product->{ProductConstant::IMG_URL};
        $productDto->{ProductConstant::IS_ACTIVED} = $product->{ProductConstant::IS_ACTIVED};

        return response()->json([
            'data' => $productDto
        ], 200);
    }
}
