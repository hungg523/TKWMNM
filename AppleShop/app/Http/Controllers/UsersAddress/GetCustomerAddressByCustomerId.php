<?php

namespace App\Http\Controllers\UsersAddress;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Models\Users;
use App\Constants\UsersAddress\UsersAddressConstant;
use App\Constants\Users\UserConstant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GetCustomerAddressByCustomerId extends Controller
{
    public function getcustomer($customerId): JsonResponse
    {
        // Validation
        $validator = Validator::make([UserConstant::USER_ID => $customerId], [
            UserConstant::USER_ID => 'required|integer|exists:users,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Users::find($customerId);
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found.'
                ], 404);
            }

            $customerAddresses = UserAddress::where(UsersAddressConstant::USER_ID, $customerId)->get();
            if ($customerAddresses->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer addresses not found.'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $customerAddresses
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching customer addresses.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
