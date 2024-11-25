<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Constants\Order\OrderConstant;
use App\Constants\OrderItem\OrderItemConstant;
use App\Constants\Coupon\CouponConstant;
use App\Enums\OrderStatus;
use App\Models\Product;

class CreateOrderController extends Controller
{
    // Form Request Class Definition
    public function createOrderRequest()
    {
        return new class extends FormRequest {
            public function authorize()
            {
                return true;
            }

            public function rules()
            {
                return [
                    OrderConstant::USER_ID => 'required|integer',
                    'order_items' => 'required|array',
                    'order_items.*.' . OrderItemConstant::PRODUCT_ID => 'required|integer|exists:products,id',
                    'order_items.*.' . OrderItemConstant::QUANTITY => 'required|integer|min:1',
                    CouponConstant::COUPON_CODE => 'nullable|string|exists:coupons,' . CouponConstant::COUPON_CODE,
                    OrderConstant::USER_ADDRESS_ID => 'required|integer',
                    OrderConstant::PAYMENT => 'required|string',
                ];
            }
        };
    }

    // Controller Method to Handle the Create Order Request
    public function create(Request $request)
    {
        // Manually instantiate and validate CreateOrderRequest
        $createOrderRequest = $this->createOrderRequest();
        $createOrderRequest->setContainer(app())->setRedirector(app('redirect'));
        
        //$validatedData = $createOrderRequest->validateResolved();

        DB::beginTransaction();
        try {
            $coupon = null;

            // Create order
            $order = new Order();
            $order->{OrderConstant::USER_ID} = $request->{OrderConstant::USER_ID};
            $order->{OrderConstant::USER_ADDRESS_ID} = $request->{OrderConstant::USER_ADDRESS_ID};
            $order->{OrderConstant::PAYMENT} = $request->{OrderConstant::PAYMENT};
            $order->{OrderConstant::STATUS} = OrderStatus::PENDING->value;
            $order->{OrderConstant::TOTAL_AMOUNT} = 0;
            $order->{OrderConstant::ORDER_DATE} = now();

            if ($request->{CouponConstant::COUPON_CODE}) {
                $coupon = Coupon::where(CouponConstant::COUPON_CODE, $request->{CouponConstant::COUPON_CODE})
                    ->whereColumn('times_used', '<', 'max_usage')
                    ->where(CouponConstant::COUPON_END_DATE, '>', now())
                    ->first();
                
                if (!$coupon) {
                    throw new \Exception('Mã giảm giá không hợp lệ.');
                }

                $order->{CouponConstant::COUPON_ID} = $coupon->{CouponConstant::COUPON_ID};
            }

            $order->save();

            $orderTotalPrice = 0;

            foreach ($request->order_items as $item) {
                $product = Product::find($item[OrderItemConstant::PRODUCT_ID]);
                if (!$product) {
                    throw new \Exception('Product not found.');
                }

                $price = $product->discount_price && $product->discount_price > 0
                    ? $product->discount_price
                    : ($product->regular_price ?? 0);

                $orderItem = new OrderItem();
                $orderItem->{OrderItemConstant::ORDER_ID} = $order->{OrderConstant::ORDER_ID};
                $orderItem->{OrderItemConstant::PRODUCT_ID} = $item[OrderItemConstant::PRODUCT_ID];
                $orderItem->{OrderItemConstant::QUANTITY} = $item[OrderItemConstant::QUANTITY];
                $orderItem->{OrderItemConstant::UNIT_PRICE} = $price;
                $orderItem->{OrderItemConstant::TOTAL} = $price * $item[OrderItemConstant::QUANTITY];
                $orderItem->save();

                $orderTotalPrice += $orderItem->{OrderItemConstant::TOTAL};
            }

            if ($coupon) {
                $discount = (float)$coupon->discount;
                $orderTotalPrice -= $discount;
                if ($orderTotalPrice < 0) {
                    $orderTotalPrice = 0;
                }

                $coupon->times_used += 1;
                $coupon->save();
            }

            $order->{OrderConstant::TOTAL_AMOUNT} = $orderTotalPrice;
            $order->save();

            DB::commit();

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
