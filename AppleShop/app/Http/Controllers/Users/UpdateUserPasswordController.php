<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
class UpdateUserPasswordController extends Controller
{
    public function updatePassword(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'string',
            'confirm_password' => 'required|string|same:password'
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
            // Find the customer by email and OTP
            $customer = Users::where('email', $request->input('email'))
                ->where('otp', $request->input('otp'))
                ->first();

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP không hợp lệ!'
                ], 409);
            }

            if ($customer->otp_expiration < now()) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP đã quá hạn!'
                ], 409);
            }

            // Update password
            $customer->password = Hash::make($request->input('password'));
            $customer->save();

            // Commit Transaction
            DB::commit();

            return response()->json(['message' => 'Password updated successfully'], Response::HTTP_OK);
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
