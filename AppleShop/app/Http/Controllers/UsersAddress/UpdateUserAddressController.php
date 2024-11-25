<?php
namespace App\Http\Controllers\UsersAddress;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\UsersAddress\UsersAddressConstant;
use Illuminate\Http\Response;

class UpdateUserAddressController extends Controller
{
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            UsersAddressConstant::USER_ID => 'required|integer',
            UsersAddressConstant::WARD => 'required|string|max:255',
            UsersAddressConstant::DISTRICT => 'required|string|max:255',
            UsersAddressConstant::PROVINCE => 'required|string|max:255',
            UsersAddressConstant::TEL => 'required|string|max:20',
            UsersAddressConstant::IS_ACTIVED => 'required|boolean',
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
            $userAddress = UserAddress::find($id);
            if (!$userAddress) {
                return response()->json([
                    'status' => false,
                    'message' => 'User address not found.'
                ], 404);
            }

            $userAddress->user_id = $request->input('user_id', $userAddress->user_id);
            $userAddress->ward = $request->input('ward', $userAddress->ward);
            $userAddress->district = $request->input('district', $userAddress->district);
            $userAddress->province = $request->input('province', $userAddress->province);
            $userAddress->tel = $request->input('tel', $userAddress->tel);
            $userAddress->is_actived = $request->input('is_actived', $userAddress->is_actived);

            // Save changes
            $userAddress->save();

            // Commit Transaction
            DB::commit();

            return response()->json(['message' => 'UserAddress update successfully'], Response::HTTP_CREATED);
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
