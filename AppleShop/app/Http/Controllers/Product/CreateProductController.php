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

class CreateProductController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    // Create a new product
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                ProductConstant::CATEGORY_ID => 'required|integer',
                ProductConstant::PRODUCT_NAME => 'required|string|max:255',
                ProductConstant::PRODUCT_DESCRIPTION => 'nullable|string',
                ProductConstant::PRODUCT_PRICE => 'required|numeric',
                ProductConstant::PRODUCT_DISCOUNT => 'nullable|numeric',
                ProductConstant::PRODUCT_QUANTITY => 'required|integer',
                ProductConstant::IS_ACTIVED => 'required|boolean',
                ProductConstant::PRODUCT_COLOR => 'nullable|string',
                'imageData',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            // Create product instance
            $product = new Product();

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
            $product->{ProductConstant::CATEGORY_ID} = $request->{ProductConstant::CATEGORY_ID};
            $product->{ProductConstant::PRODUCT_NAME} = $request->{ProductConstant::PRODUCT_NAME};
            $product->{ProductConstant::PRODUCT_DESCRIPTION} = $request->{ProductConstant::PRODUCT_DESCRIPTION};
            $product->{ProductConstant::PRODUCT_PRICE} = $request->{ProductConstant::PRODUCT_PRICE};
            $product->{ProductConstant::PRODUCT_DISCOUNT} = $request->{ProductConstant::PRODUCT_DISCOUNT};
            $product->{ProductConstant::PRODUCT_QUANTITY} = $request->{ProductConstant::PRODUCT_QUANTITY};
            $product->{ProductConstant::IS_ACTIVED} = $request->{ProductConstant::IS_ACTIVED};
            $product->{ProductConstant::PRODUCT_COLOR} = $request->{ProductConstant::PRODUCT_COLOR};
            $product->{ProductConstant::IMG_URL} = $filePath;
            // Save product to the database


            $product->save();
            DB::commit();

            return response()->json(['message' => 'Product created successfully'], Response::HTTP_CREATED);
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
