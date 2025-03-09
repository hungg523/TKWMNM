<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Constants\Order\OrderConstant;
use App\Constants\OrderItem\OrderItemConstant;
use App\Constants\Coupon\CouponConstant;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Validator;

class CreateOrderController extends Controller
{
    public function create(Request $request)
    {      
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                OrderConstant::USER_ID => 'required|integer',
                'order_items' => 'required|array',
                'order_items.*.' . OrderItemConstant::PRODUCT_ID => 'required|integer|min:1',
                'order_items.*.' . OrderItemConstant::QUANTITY => 'required|integer|min:1',
                CouponConstant::COUPON_CODE => 'nullable|string',
                OrderConstant::USER_ADDRESS_ID => 'required|integer',
                OrderConstant::PAYMENT => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $allowedPayments = ['cod', 'vnpay'];
            if (!in_array($request->{OrderConstant::PAYMENT}, $allowedPayments)) {
                throw new \Exception('Phương thức thanh toán không hợp lệ.');
            }

            $coupon = null;
            $order = new Order();
            $order->{OrderConstant::USER_ID} = $request->{OrderConstant::USER_ID};
            $order->{OrderConstant::USER_ADDRESS_ID} = $request->{OrderConstant::USER_ADDRESS_ID};
            $order->{OrderConstant::PAYMENT} = $request->{OrderConstant::PAYMENT};
            $order->{OrderConstant::STATUS} = OrderStatus::PENDING->value;
            $order->{OrderConstant::TOTAL_AMOUNT} = 0;
            $order->{OrderConstant::IS_ACTIVED} = 1;
            $order->{OrderConstant::ORDER_DATE} = now();
            $order->save();

            $orderTotalPrice = 0;

            foreach ($request->order_items as $item) {
                $product = Product::find($item[OrderItemConstant::PRODUCT_ID]);
                if (!$product) {
                    throw new \Exception("Sản phẩm ID {$item[OrderItemConstant::PRODUCT_ID]} không tồn tại.");
                }

                if ($product->stock < $item[OrderItemConstant::QUANTITY]) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
                }

                $price = $product->price && $product->discount > 0 ? $product->discount : ($product->price ?? 0);
                $orderItem = new OrderItem();
                $orderItem->{OrderItemConstant::ORDER_ID} = $order->{OrderConstant::ORDER_ID};
                $orderItem->{OrderItemConstant::PRODUCT_ID} = $item -> {OrderItemConstant::PRODUCT_ID};
                $orderItem->{OrderItemConstant::QUANTITY} = $item -> {OrderItemConstant::QUANTITY};
                $orderItem->{OrderItemConstant::UNIT_PRICE} = $price;
                $orderItem->{OrderItemConstant::TOTAL} = $price * $item[OrderItemConstant::QUANTITY];
                $orderItem->save();

                $orderTotalPrice += $orderItem->{OrderItemConstant::TOTAL};

                $product->stock -= $item[OrderItemConstant::QUANTITY];
                $product->save();
            }

            $order->{OrderConstant::TOTAL_AMOUNT} = $orderTotalPrice;
            $order->save();
            DB::commit();

            if ($request->{OrderConstant::PAYMENT} === 'vnpay') {
                return $this->createVNPayPayment($order);
            }

            return response()->json(['success' => true, 'order_id' => $order->{OrderConstant::ORDER_ID}], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Có lỗi xảy ra trong quá trình đặt hàng",
                'error_detail' => $e->getMessage()
            ], 400);
        }
    }

    private function createVNPayPayment($order)
    {
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_Url = env('VNPAY_URL');
        $vnp_Returnurl = env('VNPAY_RETURN_URL');

        $vnp_TxnRef = $order->{OrderConstant::ORDER_ID};
        $vnp_OrderInfo = "Thanh toán đơn hàng {$order->{OrderConstant::ORDER_ID}}";
        $vnp_Amount = $order->{OrderConstant::TOTAL_AMOUNT} * 100;
        $vnp_Locale = "vn";
        $vnp_BankCode = "";

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => $vnp_BankCode,
        );

        ksort($inputData);
        $query = http_build_query($inputData);
        $hashdata = urldecode($query);
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        $vnp_Url .= "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;

        return response()->json(["payment_url" => $vnp_Url]);
    }
}