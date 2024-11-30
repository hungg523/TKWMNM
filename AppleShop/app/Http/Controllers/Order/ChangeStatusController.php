<?php

namespace App\Http\Controllers\Order;
use App\Http\Controllers\Controller;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Order\OrderConstant;

class ChangeStatusController extends Controller
{
    public function changestatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                OrderConstant::ORDER_ID => 'required|integer',
                OrderConstant::STATUS => 'string'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }


            $order = Order::find($id);
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $order->status = $request->input(OrderConstant::STATUS);
            $order->save();

            DB::commit();

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
