<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Constants\Users\UserConstant;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ResentOtpUserController extends Controller
{
    public function resendOtp(Request $request)
    {
        DB::beginTransaction();
        try {
            Log::info('Start resend OTP request', ['request' => $request->all()]);

            $customer = Users::where(UserConstant::USER_EMAIL, $request->input(UserConstant::USER_EMAIL))
                ->where(UserConstant::USER_IS_ACTIVED, false)
                ->first();

            if (!$customer) {
                return response()->json([
                    'error' => 'Customer not found or already active.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Tạo OTP mới và cập nhật thời gian hết hạn
            $otp = strtoupper(Str::random(6));
            $customer->{UserConstant::OTP} = $otp;
            $customer->{UserConstant::OTP_EXPIRATION} = now()->addSeconds(90);
            $customer->save();

            // Gửi email OTP
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

            Log::info('Sending OTP email', ['email' => $request->input(UserConstant::USER_EMAIL)]);

            // Gửi email thông qua Laravel Mail
            Mail::send([], [], function ($message) use ($request, $subject, $body) {
                $message->to($request->input(UserConstant::USER_EMAIL))
                    ->subject($subject)
                    ->html($body);
            });
            DB::commit();
            Log::info('OTP resend completed successfully');
            return response()->json(['message' => 'OTP has been resent successfully. Please check your email.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Rollback nếu gặp lỗi
            DB::rollBack();
            Log::error('Error occurred during OTP resend', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'An error occurred while resending OTP.',
                'details' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
