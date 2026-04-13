<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageOptimizerService
{
    /**
     * Store and optimize an uploaded image.
     *
     * @param UploadedFile $file The uploaded file
     * @param string $directory The storage directory (e.g., 'doctors', 'hospitals/gallery')
     * @param int|null $maxWidth The maximum width to scale down to (keeps aspect ratio)
     * @param int $quality The WEBP compression quality (0-100)
     * @return string The generated storage path (e.g., 'doctors/uuid.webp')
     */
    public static function storeAndOptimize(UploadedFile $file, string $directory, ?int $maxWidth = 1200, int $quality = 80): string
    {
        // 1. Ensure directory exists
        Storage::disk('public')->makeDirectory($directory);

        // 2. Generate a unique filename with .webp extension
        $filename = Str::uuid() . '.webp';
        $path = $directory . '/' . $filename;
        $absolutePath = Storage::disk('public')->path($path);

        // 3. Process the image using GD Driver
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        // 4. Resize if width is larger than max width (scale down only)
        if ($maxWidth && $image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        // 5. Convert to WEBP and save
        $image->toWebp($quality)->save($absolutePath);

        return $path;
    }
}
