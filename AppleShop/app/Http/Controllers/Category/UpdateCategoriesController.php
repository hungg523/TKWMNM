<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Constants\Categories\CategoriesConstant;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class UpdateCategoriesController extends Controller
{
    // Update an existing category
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                CategoriesConstant::CATEGORY_NAME => 'sometimes|required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Find category by ID
            $category = Categories::find($id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
            }

            // Update category fields
            $category->{CategoriesConstant::CATEGORY_NAME} = $request->{CategoriesConstant::CATEGORY_NAME} ?? $category->{CategoriesConstant::CATEGORY_NAME};

            // Save changes to the database
            $category->save();
            DB::commit();

            return response()->json(['message' => 'Category updated successfully'], Response::HTTP_OK);
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
