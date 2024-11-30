<?php

namespace App\Http\Controllers\UsersAddress;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\UsersAddress\UsersAddressConstant;
use Illuminate\Http\Response;

class CreateUserAddressController extends Controller
{
    /**
     * Create a new customer address.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            UsersAddressConstant::USER_ID => 'required|integer',
            UsersAddressConstant::WARD => 'required|string|max:255',
            UsersAddressConstant::DISTRICT => 'required|string|max:255',
            UsersAddressConstant::PROVINCE => 'required|string|max:255',
            UsersAddressConstant::TEL => 'required|string|max:20',
            UsersAddressConstant::IS_ACTIVED => 'required|boolean',
            UsersAddressConstant::FULL_NAME => 'required|string',
            UsersAddressConstant::ADDRESS => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Start Transaction
        DB::beginTransaction();
        try {
            // Create new customer address
            $customerAddress = new UserAddress();
            $customerAddress->user_id = $request->input(UsersAddressConstant::USER_ID);
            $customerAddress->ward = $request->input(UsersAddressConstant::WARD);
            $customerAddress->district = $request->input(UsersAddressConstant::DISTRICT);
            $customerAddress->province = $request->input(UsersAddressConstant::PROVINCE);
            $customerAddress->tel = $request->input(UsersAddressConstant::TEL);
            $customerAddress->is_actived = $request->input(UsersAddressConstant::IS_ACTIVED);
            $customerAddress->full_name = $request->input(UsersAddressConstant::FULL_NAME);
            $customerAddress->address = $request->input(UsersAddressConstant::ADDRESS);
            $customerAddress->save();

            // Commit Transaction
            DB::commit();

            return response()->json(['message' => 'UserAddress created successfully'], Response::HTTP_CREATED);
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
