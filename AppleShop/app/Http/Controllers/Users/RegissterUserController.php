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
use Illuminate\Support\Facades\Log;
use App\Services\FileService;
use App\Enums\AssetType;

class RegissterUserController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    // Register a new customer
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {

            // Validate the request
            $validator = Validator::make($request->all(), [
                UserConstant::USER_EMAIL => 'required|email',
                UserConstant::USER_PASSWORD => 'required|string',
                'imageData'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $user = Users::where(UserConstant::USER_EMAIL, $request->input(UserConstant::USER_EMAIL))
                ->where(UserConstant::USER_IS_ACTIVED, false)
                ->first();
            // Xử lý upload file nếu có
            if (!empty($request['imageData'])) {
                $newFileExtension = strtolower($this->fileService->getFileExtensionFromBase64($request['imageData']));
                $currentFileName = !empty($user->{UserConstant::USER_IMG_AVATAR}) ? basename($user->{UserConstant::USER_IMG_AVATAR}) : null;
                $currentFileExtension = $currentFileName ? strtolower(pathinfo($currentFileName, PATHINFO_EXTENSION)) : null;

                if ($currentFileName && ".".$currentFileExtension === $newFileExtension) {
                    $fileName = $currentFileName;
                } else {
                    $fileName = substr((string) Str::uuid(), 0, 4) . $newFileExtension;
                }

                $filePath = $this->fileService->uploadFile($fileName, $request['imageData'], AssetType::USER_IMG -> value);
            }
            $otp = strtoupper(Str::random(6));
            $username = "user_".str::random(10);

            if ($user) {
                $user->{UserConstant::USER_USERNAME} = $username;
                $user->{UserConstant::USER_PASSWORD} = bcrypt($request->input(UserConstant::USER_PASSWORD));
                $user->{UserConstant::OTP} = $otp;
                $user->{UserConstant::USER_IMG_AVATAR} = $filePath ?? null;
                $user->{UserConstant::OTP_EXPIRATION} = now()->addSeconds(5);
                $user->save();
            } else {
                $user = new Users();
                $user->{UserConstant::USER_USERNAME} = $username;
                $user->{UserConstant::USER_EMAIL} = $request->input(UserConstant::USER_EMAIL);
                $user->{UserConstant::USER_PASSWORD} = bcrypt($request->input(UserConstant::USER_PASSWORD));
                $user->{UserConstant::OTP} = $otp;
                $user->{UserConstant::USER_IMG_AVATAR} = $filePath ?? null;
                $user->{UserConstant::OTP_EXPIRATION} = now()->addSeconds(90);
                $user->{UserConstant::USER_ROLES} = 0;
                $user->{UserConstant::USER_IS_ACTIVED} = false;
                $user->{UserConstant::CREATE_AT} = now();

                $user->save();
            }
            // Email subject and body
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

            // Sending email
            Mail::send([], [], function ($message) use ($request, $subject, $body) {
                $message->to($request->input(UserConstant::USER_EMAIL))
                    ->subject($subject)
                    ->html($body);
            });

            DB::commit();
            return response()->json(['message' => 'Customer registered successfully. Please check your email for OTP.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error occurred during registration', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
