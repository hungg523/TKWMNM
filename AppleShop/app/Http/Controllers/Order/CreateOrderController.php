<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
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
                throw new Exception('Phương thức thanh toán không hợp lệ.');
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

                // if ($product->stock < $item[OrderItemConstant::QUANTITY]) {
                //     throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho.");
                // }

                // $price = $product->price && $product->discount > 0 ? $product->discount : ($product->price ?? 0);
                $orderItem = new OrderItem();
                $orderItem->{OrderItemConstant::ORDER_ID} = $order->{OrderConstant::ORDER_ID};
                // $orderItem->{OrderItemConstant::PRODUCT_ID} = $item -> {OrderItemConstant::PRODUCT_ID};
                $orderItem->{OrderItemConstant::PRODUCT_ID} = 1;
                // $orderItem->{OrderItemConstant::QUANTITY} = $item -> {OrderItemConstant::QUANTITY};
                $orderItem->{OrderItemConstant::QUANTITY} = 10;
                $orderItem->{OrderItemConstant::UNIT_PRICE} = 100000;
                // $orderItem->{OrderItemConstant::TOTAL} = $price * $item[OrderItemConstant::QUANTITY];
                $orderItem->{OrderItemConstant::TOTAL} = 100000;
                $orderItem->save();

                $orderTotalPrice += $orderItem->{OrderItemConstant::TOTAL};

                // $product->stock -= $item[OrderItemConstant::QUANTITY];
                // $product->save();
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

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => intval($order->{OrderConstant::TOTAL_AMOUNT} * 100),
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan don hang " . strval($order->{OrderConstant::ORDER_ID}),
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => uniqid(), // Tạo TxnRef duy nhất
            "vnp_OrderType" => "billpayment",
            "vnp_ExpireDate" => date('YmdHis', strtotime('+1 days')),
        );

        // Xóa vnp_BankCode nếu trống
        if (empty($inputData["vnp_BankCode"])) {
            unset($inputData["vnp_BankCode"]);
        }

        // Tính SecureHash
        ksort($inputData);
        $hashData = [];
        foreach ($inputData as $key => $value) {
            if ($key != "vnp_SecureHash" && $key != "vnp_SecureHashType") {
                $hashData[] = $key . "=" . $value;
            }
        }
        $hashString = implode("&", $hashData);
        $hashString = urldecode($hashString); // Giữ nguyên format
        $vnpSecureHash = hash_hmac('sha512', $hashString, env('VNPAY_HASH_SECRET'));
        $inputData["vnp_SecureHash"] = $vnpSecureHash;

        $paymentUrl = $vnp_Url . "?" . http_build_query($inputData);

        

        if ($vnpSecureHash !== $inputData['vnp_SecureHash']) {
            dd("❌ Chữ ký bị thay đổi trước khi gửi!", $hashString, $vnpSecureHash, $inputData['vnp_SecureHash']);
        }
        // Trả về URL thanh toán
        return response()->json(['success' => true, "payment_url" => $paymentUrl], 200);
    }
}