<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageOptimizerService
{
    /**
     * Upload and optimize an image.
     * 
     * @param UploadedFile $file The uploaded file.
     * @param string $directory The directory in storage/app/public to save the file.
     * @param int $maxWidth The maximum width of the image.
     * @param int $quality The quality of the WebP encoding (0-100).
     * @return string The relative path to the saved image.
     */
    public static function uploadAndOptimize(UploadedFile $file, string $directory, int $maxWidth = 1200, int $quality = 80): string
    {
        try {
            // Init Image Manager
            /** @var \Intervention\Image\ImageManager $manager */
            $manager = new ImageManager(new Driver());
            
            // Read image from file
            $image = $manager->decodePath($file->getRealPath());
            
            // Scale down if image is larger than maxWidth
            if ($image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }
            
            // Encode to WebP format
            $encoded = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: $quality));
            
            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.webp';
            $path = $directory . '/' . $filename;
            
            // Save to public storage
            Storage::disk('public')->put($path, (string) $encoded);
            
            return $path;
        } catch (\Throwable $e) {
            // Fallback: Jika gagal optimize (misal format tidak didukung seperti PDF, HEIC), simpan aslinya
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = $directory . '/' . $filename;
            
            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            
            return $path;
        }
    }
}
