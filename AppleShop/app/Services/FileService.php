<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class FileService
{
    protected $assetServerUrl;

    public function __construct()
    {
        // Đọc URL của asset server từ file cấu hình .env hoặc config
        $this->assetServerUrl = config('services.asset_service.url');
        $this->assetDirectory = config('services.asset_service.directory');
    }

    /**
     * Upload file thông qua HTTP request
     *
     * @param string $fileName Tên file
     * @param string $base64String Nội dung của file dạng base64
     * @param int $type Loại tài sản (Asset Type)
     * @return string
     * @throws Exception
     */
    public function uploadFile(string $fileName, string $base64String, int $type)
    {
        try {
            $requestContent = [
                'FileName' => $fileName,
                'Content' => $this->getBase64Data($base64String),
                'AssetType' => $type,
            ];

            // Gửi yêu cầu POST tới Asset Server
            $response = Http::withOptions(['verify' => false])->post($this->assetServerUrl . '/upload', $requestContent);

            // Kiểm tra nếu yêu cầu thất bại
            if ($response->failed()) {
                throw new Exception("Validate Fail", 400);
            }

            $responseData = $response->json();

            return $responseData['data'] ?? null;
        } catch (Exception $e) {
            Log::error("Error uploading file: " . $e->getMessage());
            throw new Exception("Internal Server Error", 500);
        }
    }

    /**
     * Lấy dữ liệu từ base64 string
     *
     * @param string $base64String
     * @return string
     */
    public function getBase64Data(string $base64String)
    {
        $parts = explode(',', $base64String);
        return count($parts) > 1 ? $parts[1] : $base64String;
    }

    /**
     * Lấy phần mở rộng của file từ base64 string
     *
     * @param string $base64String
     * @return string
     */
    public function getFileExtensionFromBase64(string $base64String)
    {
        $mime = $this->getMimeTypeFromBase64($base64String);
        $mimeTypes = [
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
            'image/bmp' => '.bmp',
            'image/webp' => '.webp',
        ];

        return $mimeTypes[$mime] ?? '';
    }

    /**
     * Lấy MIME type từ base64 string
     *
     * @param string $base64String
     * @return string|null
     */
    private function getMimeTypeFromBase64(string $base64String)
    {
        if (strpos($base64String, ',') !== false) {
            $mimeType = explode(',', $base64String)[0];
            $mimeType = explode(':', $mimeType)[1];
            return explode(';', $mimeType)[0];
        }

        return null;
    }
}
