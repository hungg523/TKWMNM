<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Constants\Users\UserConstant;

class LoginUserController extends Controller
{
    public function login(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                UserConstant::USER_EMAIL => 'required|email',
                UserConstant::USER_PASSWORD => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $customer = Users::where(UserConstant::USER_EMAIL, $request->input(UserConstant::USER_EMAIL))->first();

            if (!$customer) {
                return response()->json(['error' => 'Customer not found.'], Response::HTTP_NOT_FOUND);
            }

            if ($customer->is_active) {
                return response()->json(['error' => 'Account is not activated.'], Response::HTTP_FORBIDDEN);
            }

            if (!Hash::check($request->input('password'), $customer->password)) {
                return response()->json(['error' => 'Wrong Password.'], Response::HTTP_UNAUTHORIZED);
            }

            DB::commit();
            return response()->json(['message' => 'Login successful.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred during login.',
                'details' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
