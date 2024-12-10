<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Constants\Product\ProductConstant;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Services\FileService;
use App\Enums\AssetType;
use Illuminate\Support\Str;
class UpdateProductController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                ProductConstant::PRODUCT_NAME => 'sometimes|required|string|max:255',
                ProductConstant::PRODUCT_DESCRIPTION => 'nullable|string',
                ProductConstant::PRODUCT_PRICE => 'sometimes|required|numeric',
                ProductConstant::PRODUCT_DISCOUNT => 'nullable|numeric',
                ProductConstant::PRODUCT_QUANTITY => 'sometimes|required|integer',
                ProductConstant::IS_ACTIVED => 'sometimes|required|boolean',
                ProductConstant::PRODUCT_COLOR => 'nullable|string',
                ProductConstant::CATEGORY_ID => 'nullable|',
                'imageData',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $product = Product::find($id);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
            }
            if (!empty($request['imageData'])) {
                $newFileExtension = strtolower($this->fileService->getFileExtensionFromBase64($request['imageData']));
                $currentFileName = !empty($product->{ProductConstant::IMG_URL}) ? basename($product->{ProductConstant::IMG_URL}) : null;
                $currentFileExtension = $currentFileName ? strtolower(pathinfo($currentFileName, PATHINFO_EXTENSION)) : null;

                if ($currentFileName && "." . $currentFileExtension === $newFileExtension) {
                    $fileName = $currentFileName;
                } else {
                    $fileName = substr((string) Str::uuid(), 0, 4) . $newFileExtension;
                }

                $filePath = $this->fileService->uploadFile($fileName, $request['imageData'], AssetType::PRODUCT_IMG->value);
            }

            $product->{ProductConstant::PRODUCT_NAME} = $request->{ProductConstant::PRODUCT_NAME} ?? $product->{ProductConstant::PRODUCT_NAME};
            $product->{ProductConstant::PRODUCT_DESCRIPTION} = $request->{ProductConstant::PRODUCT_DESCRIPTION} ?? $product->{ProductConstant::PRODUCT_DESCRIPTION};
            $product->{ProductConstant::PRODUCT_PRICE} = $request->{ProductConstant::PRODUCT_PRICE} ?? $product->{ProductConstant::PRODUCT_PRICE};
            $product->{ProductConstant::PRODUCT_DISCOUNT} = $request->{ProductConstant::PRODUCT_DISCOUNT} ?? $product->{ProductConstant::PRODUCT_DISCOUNT};
            $product->{ProductConstant::PRODUCT_QUANTITY} = $request->{ProductConstant::PRODUCT_QUANTITY} ?? $product->{ProductConstant::PRODUCT_QUANTITY};
            $product->{ProductConstant::IS_ACTIVED} = $request->{ProductConstant::IS_ACTIVED} ?? $product->{ProductConstant::IS_ACTIVED};
            $product->{ProductConstant::PRODUCT_COLOR} = $request->{ProductConstant::PRODUCT_COLOR} ?? $product->{ProductConstant::PRODUCT_COLOR};
            $product->{ProductConstant::CATEGORY_ID} = $request->{ProductConstant::CATEGORY_ID} ?? $product->{ProductConstant::CATEGORY_ID};
            $product->{ProductConstant::IMG_URL} = $filePath ?? $product->{ProductConstant::IMG_URL};

            $product->save();

            DB::commit();

            return response()->json(['message' => 'Product updated successfully'], Response::HTTP_OK);
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
