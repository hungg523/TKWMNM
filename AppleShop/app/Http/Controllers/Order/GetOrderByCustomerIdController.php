<?php

namespace App\Http\Controllers\Order;
use App\Http\Controllers\Controller;

use App\Models\Order;
use Illuminate\Http\Request;

class GetOrderByCustomerIdController extends Controller
{
    public function getordersbycustomerid($customerId)
    {
        // Eager Loading các quan hệ để tránh N+1 query problem
        $orders = Order::with(['items', 'user_address', 'coupon', 'user'])
            ->where('user_id', $customerId)
            ->get();

        // Nếu không tìm thấy đơn hàng
        if ($orders->isEmpty()) {
            return response()->json(['error' => 'Orders not found'], 404);
        }

        // Chuyển đổi dữ liệu đơn hàng sang DTO
        $orderDtos = $orders->map(function ($order) {
            return [
                'id' => $order->order_id,
                'email' => $order->user ? $order->user->email : null,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'address' => $order->user_address ? [
                    'id' => $order->user_address->user_address_id,
                    'full_name' => $order->user_address->full_name,
                    'phone' => $order->user_address->tel,
                    'final_address' => "{$order->user_address->address}, {$order->user_address->ward}, {$order->user_address->district}, {$order->user_address->province}",
                ] : null,
                'coupon' => $order->coupon ? [
                    'id' => $order->coupon->coupon_id,
                    'description' => $order->coupon->description,
                    'discount_percent' => $order->coupon->discount_percent . "%",
                ] : null,
                'order_items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->quantity * $item->unit_price,
                    ];
                })->toArray()
            ];
        })->toArray();

        return response()->json($orderDtos);
    }
}
