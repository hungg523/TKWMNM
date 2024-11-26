<?php

namespace App\Http\Controllers\Order;
use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class GetOrderItemByOrderId extends Controller
{
    public function getorderitemsbyorderid($orderId)
    {
        // Kiểm tra nếu đơn hàng có tồn tại hay không
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Lấy danh sách order items liên quan đến đơn hàng
        $orderItems = OrderItem::where('order_id', $orderId)->get();

        if ($orderItems->isEmpty()) {
            return response()->json(['error' => 'Order items not found'], 404);
        }

        return response()->json($orderItems);
    }
}
