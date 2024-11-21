<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;

use App\Models\Categories;
use Illuminate\Http\Response;

class GetByIdCategoriesController extends Controller
{
    public function show($id)
    {
        try {
            // Find category by ID
            $category = Categories::find($id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($category, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'isSuccess' => false,
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'stageTrace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
