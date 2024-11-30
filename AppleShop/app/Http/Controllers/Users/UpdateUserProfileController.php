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
use Illuminate\Support\Facades\Log;

class UpdateUserProfileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function updateProfile(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                UserConstant::USER_USERNAME => 'sometimes|required|string|max:255',
                UserConstant::USER_PHONE_NUMBER => 'sometimes|required|string|max:15',
                UserConstant::USER_DATE_OF_BIRTH => 'nullable|date',
                'imageData',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Find user by ID
            $user = Users::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Handle image upload if image data is provided
            if (!empty($request['imageData'])) {
                $newFileExtension = strtolower($this->fileService->getFileExtensionFromBase64($request['imageData']));
                $currentFileName = !empty($user->{UserConstant::USER_IMG_AVATAR}) ? basename($user->{UserConstant::USER_IMG_AVATAR}) : null;
                $currentFileExtension = $currentFileName ? strtolower(pathinfo($currentFileName, PATHINFO_EXTENSION)) : null;

                if ($currentFileName && "." . $currentFileExtension === $newFileExtension) {
                    $fileName = $currentFileName;
                } else {
                    $fileName = substr((string) Str::uuid(), 0, 4) . $newFileExtension;
                }
                //dd($newFileExtension, $currentFileName, $currentFileExtension, $fileName);
                $filePath = $this->fileService->uploadFile($fileName, $request['imageData'], AssetType::USER_IMG->value);
            }

            $user->username = $request->input(UserConstant::USER_USERNAME, $user->username);
            $user->phone_number = $request->input(UserConstant::USER_PHONE_NUMBER, $user->phone_number);
            $user->date_of_birth = $request->input(UserConstant::USER_DATE_OF_BIRTH, $user->date_of_birth);
            $user->{UserConstant::USER_IMG_AVATAR} = $filePath ?? null;
            // Save updated user
            $user->save();

            // Commit transaction
            DB::commit();

            return response()->json(['message' => 'Profile updated successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Rollback if there is any exception
            DB::rollBack();

            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
