<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Constants\Categories\CategoriesConstant;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class CreateCategoriesController extends Controller
{
    // Create a new category
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                CategoriesConstant::CATEGORY_NAME => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Create category instance
            $category = new Categories();
            $category->{CategoriesConstant::CATEGORY_NAME} = $request->{CategoriesConstant::CATEGORY_NAME};

            // Save category to the database
            $category->save();
            DB::commit();

            return response()->json(['message' => 'Category created successfully'], Response::HTTP_CREATED);
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
