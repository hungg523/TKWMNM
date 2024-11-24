<?php
namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Response;

class GetByIdUserController extends Controller
{
    public function show($id)
    {
        try {
            $user = Users::find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($user, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
