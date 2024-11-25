<?php

namespace App\Http\Controllers\UsersAddress;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GetAllUserAddressController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $customerAddresses = UserAddress::all();
            return response()->json([
                'status' => true,
                'data' => $customerAddresses
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
