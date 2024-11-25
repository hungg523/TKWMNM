<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Constants\Users\UserConstant;

class ChangePasswordController extends Controller
{

    public function changePassword(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            UserConstant::USER_EMAIL => 'required|email|exists:users,email',
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
            // Find the customer by email
            $customer = Users::where(UserConstant::USER_EMAIL, $request->input(UserConstant::USER_EMAIL))->where(UserConstant::USER_IS_ACTIVED, true)->first();
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Generate OTP
            $otp = strtoupper(Str::random(6));
            $customer->otp = $otp;
            $customer->otp_expiration = now()->addSeconds(90);
            $customer->save();

            // Send email
            $subject = "Xác thực tài khoản!";
            $body = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
                <div style='padding: 20px; border-bottom: 3px solid #007BFF; text-align: center;'>
                    <img src='https://drive.google.com/uc?export=view&id=1Rk_q7FCRzk5GG7tJDDAVjMceA6hO7WIY' alt='Logo'
                        style='width: 150px; margin-bottom: 10px;' />
                    <h2 style='color: #007BFF; font-weight: bold; margin: 0;'>Xác thực tài khoản</h2>
                </div>
                <div style='padding: 20px;'>
                    <p>Xin chào,<br> Cảm ơn bạn đã đăng ký tài khoản tại hệ thống của chúng tôi. Để hoàn tất quá trình đăng ký, vui lòng sử dụng mã xác thực bên dưới:</p>
                    <div
                        style='margin: 20px 0; padding: 15px; background-color: #e9ecef; border-radius: 8px; text-align: center; font-size: 24px; font-weight: bold; color: #007BFF;'>
                        {$otp}
                    </div>
                    <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
                    <p>Trân trọng,<br />HHT_AppleShop!</p>
                </div>
                <div
                    style='background-color: #343a40; color: white; padding: 10px; text-align: center; font-size: 12px; border-top: 3px solid #007BFF;'>
                    © 2024 HHT_AppleShop
                </div>
            </div>​";

            Mail::send([], [], function ($message) use ($request, $subject, $body) {
                $message->to($request->input(UserConstant::USER_EMAIL))
                    ->subject($subject)
                    ->html($body);
            });

            // Commit Transaction
            DB::commit();

            return response()->json(['message' => ' Send OTP successfully'], Response::HTTP_OK);
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
