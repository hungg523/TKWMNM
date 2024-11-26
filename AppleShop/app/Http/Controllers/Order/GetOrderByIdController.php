<?php

namespace App\Http\Controllers\Order;
use App\Http\Controllers\Controller;

use App\Models\Order;

class GetOrderByIdController extends Controller
{
    public function getorderbyid($id)
    {
        $order = Order::with(['items', 'user_address', 'coupon', 'user'])->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        // Tạo DTO cho Order
        $orderDTO = [
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
                'discount_percent' => $order->coupon->discount_percent."%",
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

        return response()->json($orderDTO);
    }
}
