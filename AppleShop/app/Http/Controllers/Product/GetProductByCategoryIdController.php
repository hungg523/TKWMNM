<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;

use App\Models\Categories;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Constants\Categories\CategoriesConstant;
use Illuminate\Support\Facades\DB;

class GetProductByCategoryIdController extends Controller
{
    public function getProductsByCategoryId(Request $request, $categoryId)
    {
        try {
            $category = Categories::find($categoryId);
            if (!$category) {
                return response()->json(['message' => 'Danh mục không tồn tại'], 404);
            }


            $productsQuery = Product::where('category_id', $categoryId);

            $totalCount = $productsQuery->count();

            $pageSize = $request->input('page_size', 10);
            $pageNumber = $request->input('page_number', 1);

            $products = $productsQuery
                ->skip(($pageNumber - 1) * $pageSize)
                ->take($pageSize)
                ->get();

            $successfulOrders = Order::where('status', 'successed')->pluck('order_id')->toArray();

            $orderItems = OrderItem::whereIn('order_id', $successfulOrders)->get();

            $productSaleCount = $orderItems->groupBy('product_id')->map(function ($items) {
                return $items->sum('quantity');
            });
            $products = $products->map(function ($product) use ($productSaleCount) {
                $product->amount_seller = $productSaleCount->get($product->id, 0);
                return $product;
            });

            return response()->json([
                'data' => $products,
                'page_number' => $pageNumber,
                'page_size' => $pageSize,
                'total_count' => $totalCount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
        }
    }
}
