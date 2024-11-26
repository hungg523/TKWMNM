<?php

namespace App\Http\Controllers\Order;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Response;


class GetAllOrderController extends Controller
{
    // Get all products
    public function index()
    {
        try {
            // Retrieve all products
            $products = Order::all();
            return response()->json($products, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
