<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Constants\Users\UserConstant;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateUserController extends Controller
{
    // Register a new customer
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                UserConstant::USER_USERNAME => 'nullable|string|max:255',
                UserConstant::USER_RENDER => 'required|string|max:255',
                UserConstant::USER_EMAIL => 'required|email|unique:users,email',
                UserConstant::USER_PASSWORD => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $otp = strtoupper(Str::random(6));

            $existingUser = Users::where(UserConstant::USER_EMAIL, $request->email)
                ->where(UserConstant::USER_IS_ACTIVED, false)
                ->first();

            if ($existingUser) {
                $existingUser->password = bcrypt($request->password);
                $existingUser->otp = $otp;
                $existingUser->otp_expiration = now()->addSeconds(90);
                $existingUser->save();
            } else {
                $user = new Users();
                $user->{UserConstant::USER_EMAIL} = $request->email;
                $user->{UserConstant::USER_PASSWORD} = bcrypt($request->password);
                $user->otp = $otp;
                $user->otp_expiration = now()->addSeconds(90);
                $user->{UserConstant::USER_ROLES} = 0;
                $user->{UserConstant::USER_IS_ACTIVED} = false;
                $user->created_at = now();

                $user->save();
            }

            // Email subject and body
            $subject = "Xác thực tài khoản!";
            $body = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
                <div style='padding: 20px; border-bottom: 3px solid #4CAF50; text-align: center;'>
                    <img src='https://drive.google.com/uc?export=view&id=16HsextqqzKJklrRHmX4Qi3RNRM-x1s6e' alt='Logo' style='width: 150px; margin-bottom: 10px;' />
                    <h2 style='color: #4CAF50; font-weight: bold; margin: 0;'>Xác thực tài khoản</h2>
                </div>
                <div style='padding: 20px;'>
                    <p>Xin chào,<br>
                    Cảm ơn bạn đã đăng ký tài khoản tại hệ thống của chúng tôi. Để hoàn tất quá trình đăng ký, vui lòng sử dụng mã xác thực bên dưới:</p>
                    <div style='margin: 20px 0; padding: 15px; background-color: #f8f8f8; border-radius: 8px; text-align: center; font-size: 24px; font-weight: bold; color: #4CAF50;'>
                        {$otp}
                    </div>
                    <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
                    <p>Trân trọng,<br/>HHT Pharmacy Community!</p>
                </div>
                <div style='background-color: #4CAF50; color: white; padding: 10px; text-align: center; font-size: 12px; border-top: 3px solid #4CAF50;'>
                    © 2024 HHT Pharmacy
                </div>
            </div>";

            // Sending email
            Mail::send([], [], function ($message) use ($request, $subject, $body) {
                $message->to($request->email)
                    ->subject($subject)
                    ->html($body, 'text/html');
            });

            DB::commit();

            return response()->json(['message' => 'Customer registered successfully. Please check your email for OTP.'], Response::HTTP_CREATED);
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
