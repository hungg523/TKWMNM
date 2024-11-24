<?php
namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Response;

class GetAllUserController extends Controller
{
    public function index()
    {
        try {
            $users = Users::all();
            return response()->json($users, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
