<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Constants\Users\UserConstant;
use App\Services\FileService;
use App\Enums\AssetType;
use Illuminate\Support\Facades\Validator;


class UpdateUserProfileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function updateprofile(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                UserConstant::USER_USERNAME => $request->input(UserConstant::USER_USERNAME, UserConstant::USER_USERNAME),
                UserConstant::USER_PHONE_NUMBER => $request->input(UserConstant::USER_PHONE_NUMBER, UserConstant::USER_PHONE_NUMBER),
                UserConstant::USER_DATE_OF_BIRTH => $request->input(UserConstant::USER_DATE_OF_BIRTH, UserConstant::USER_DATE_OF_BIRTH),
                'imageData'
            ]);
            $user = Users::find($id);
            if (!$user) {
                return response()->json(['message' => 'Users not found'], 404);
            }

            if (!empty($request['imageData'])) {
                $newFileExtension = strtolower($this->fileService->getFileExtensionFromBase64($request['imageData']));
                $currentFileName = !empty($user->{UserConstant::USER_IMG_AVATAR}) ? basename($user->{UserConstant::USER_IMG_AVATAR}) : null;
                $currentFileExtension = $currentFileName ? strtolower(pathinfo($currentFileName, PATHINFO_EXTENSION)) : null;

                if ($currentFileName && "." . $currentFileExtension === $newFileExtension) {
                    $fileName = $currentFileName;
                } else {
                    $fileName = substr((string) Str::uuid(), 0, 4) . $newFileExtension;
                }

                // Upload và cập nhật đường dẫn ảnh
                $filePath = $this->fileService->uploadFile($fileName, $request['imageData'], AssetType::USER_IMG->value);
                $user->{UserConstant::USER_IMG_AVATAR} = $filePath;
            }

            $user->save();
            DB::commit();

            return response()->json(['message' => 'Profile updated successfully'], Response::HTTP_OK);
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
