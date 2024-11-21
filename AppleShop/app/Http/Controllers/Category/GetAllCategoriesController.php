<?php


namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Response;

class GetAllCategoriesController extends Controller
{
    // Get all categories
    public function index()
    {
        try {
            // Retrieve all categories
            $categories = Categories::all();
            return response()->json($categories, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
