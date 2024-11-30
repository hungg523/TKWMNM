<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Constants\Order\OrderConstant;
use App\Constants\Product\ProductConstant;


class GetAllsProductController extends Controller
{
    public function getAllsProducts(Request $request)
    {
        $pageNumber = $request->input('pageNumber', 1);
        $pageSize = $request->input('pageSize', 6);

        $products = Product::where(ProductConstant::IS_ACTIVED, true)->get();

        $totalCount = $products->count();

        $pagedProducts = $products->forPage($pageNumber, $pageSize);

        $successfulOrders = Order::where(OrderConstant::STATUS, 'Successed')
            ->pluck(OrderConstant::ORDER_ID)
            ->toArray();

        $orderItems = OrderItem::whereIn(OrderConstant::ORDER_ID, $successfulOrders)->get();

        $productSaleCount = $orderItems->groupBy(ProductConstant::PRODUCT_ID)->map(function ($group) {
            return $group->sum(ProductConstant::PRODUCT_QUANTITY);
        });

        $productDtos = $pagedProducts->map(function ($product) use ($productSaleCount) {
            $productDto = new \stdClass();
            $productDto->{ProductConstant::PRODUCT_ID} = $product->{ProductConstant::PRODUCT_ID};
            $productDto->{ProductConstant::PRODUCT_NAME} = $product->{ProductConstant::PRODUCT_NAME};
            $productDto->{ProductConstant::PRODUCT_PRICE} = $product->{ProductConstant::PRODUCT_PRICE};
            $productDto->{ProductConstant::PRODUCT_DISCOUNT} = $product->{ProductConstant::PRODUCT_DISCOUNT};
            $productDto->{ProductConstant::PRODUCT_QUANTITY} = $product->{ProductConstant::PRODUCT_QUANTITY};
            $productDto->{ProductConstant::PRODUCT_DESCRIPTION} = $product->{ProductConstant::PRODUCT_DESCRIPTION};
            $productDto->{ProductConstant::IMG_URL} = $product->{ProductConstant::IMG_URL};
            $productDto->{ProductConstant::IS_ACTIVED} = $product->{ProductConstant::IS_ACTIVED};

            return $productDto;
        });

        return response()->json([
            'data' => $productDtos,
            'page_number' => $pageNumber,
            'page_size' => $pageSize,
            'total_count' => $totalCount,
        ]);
    }
}
