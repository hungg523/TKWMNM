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
                'id' => $order->id,
                'email' => $order->customer ? $order->customer->email : null,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'coupon' => $order->coupon ? [
                    'id' => $order->coupon->id,
                    'description' => $order->coupon->description,
                    'discount' => $order->coupon->discount,
                ] : null,
                'address' => $order->customerAddress ? [
                    'id' => $order->customerAddress->id,
                    'address' => $order->customerAddress->address,
                    'full_name' => $order->customerAddress->full_name,
                    'phone' => $order->customerAddress->phone,
                    'province' => $order->customerAddress->province,
                    'district' => $order->customerAddress->district,
                    'ward' => $order->customerAddress->ward,
                    'final_address' => "{$order->customerAddress->address}, {$order->customerAddress->ward}, {$order->customerAddress->district}, {$order->customerAddress->province}",
                ] : null,
                'order_items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ];
                })->toArray()
            ];
        })->toArray();

        return response()->json($orderDtos);
    }
}
