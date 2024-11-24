<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Constants\Users\UserConstant;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AuthenUserController extends Controller
{
    public function authen(Request $request)
    {
        DB::beginTransaction();
        try {
            $email = $request->input(UserConstant::USER_EMAIL);
            $otp = $request->input(UserConstant::OTP);

            $customer = Users::where(UserConstant::USER_EMAIL, $email)
                ->where(UserConstant::OTP, $otp)
                ->first();

            if (!$customer) {
                return response()->json([
                    'error' => 'Customer not found or incorrect OTP.',
                ], Response::HTTP_NOT_FOUND);
            }

            if ($customer->{UserConstant::OTP_EXPIRATION} < Carbon::now()) {
                return response()->json([
                    'error' => 'OTP has expired!',
                ], Response::HTTP_CONFLICT);
            }
            $customer->{UserConstant::USER_IS_ACTIVED} = true;
            $customer->save();

            DB::commit();
            return response()->json([
                'message' => 'Customer authenticated successfully.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred during authentication.',
                'details' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
