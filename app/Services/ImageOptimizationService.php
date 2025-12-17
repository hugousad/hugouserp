<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageOptimizationService
{
    /**
     * Maximum dimensions for different contexts
     */
    protected array $maxDimensions = [
        'thumbnail' => ['width' => 150, 'height' => 150],
        'logo' => ['width' => 400, 'height' => 200],
        'favicon' => ['width' => 64, 'height' => 64],
        'product' => ['width' => 800, 'height' => 800],
        'document' => ['width' => 1200, 'height' => 1200],
        'general' => ['width' => 1920, 'height' => 1080],
    ];

    /**
     * Quality settings for different contexts
     */
    protected array $qualitySettings = [
        'thumbnail' => 70,
        'logo' => 85,
        'favicon' => 100,
        'product' => 80,
        'document' => 85,
        'general' => 80,
    ];

    /**
     * Optimize an uploaded image
     */
    public function optimizeUploadedFile(
        UploadedFile $file,
        string $context = 'general',
        ?string $disk = 'public'
    ): array {
        if (!$this->isImage($file)) {
            return $this->storeWithoutOptimization($file, $disk);
        }

        try {
            $image = Image::make($file);
            $originalSize = $file->getSize();
            
            // Get dimensions
            $maxWidth = $this->maxDimensions[$context]['width'] ?? 1920;
            $maxHeight = $this->maxDimensions[$context]['height'] ?? 1080;
            $quality = $this->qualitySettings[$context] ?? 80;
            
            // Resize if necessary, maintaining aspect ratio
            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = 'media/' . date('Y/m') . '/' . $filename;
            
            // Encode with quality
            $encoded = $image->encode($extension, $quality);
            
            // Store the optimized image
            Storage::disk($disk)->put($path, $encoded);
            
            $optimizedSize = Storage::disk($disk)->size($path);
            
            // Generate thumbnail
            $thumbnailPath = $this->generateThumbnail($image, $disk);
            
            return [
                'file_path' => $path,
                'thumbnail_path' => $thumbnailPath,
                'size' => $originalSize,
                'optimized_size' => $optimizedSize,
                'width' => $image->width(),
                'height' => $image->height(),
                'mime_type' => $file->getMimeType(),
                'extension' => $extension,
            ];
        } catch (\Exception $e) {
            Log::error('Image optimization failed: ' . $e->getMessage());
            return $this->storeWithoutOptimization($file, $disk);
        }
    }

    /**
     * Generate a thumbnail for an image
     */
    protected function generateThumbnail($image, string $disk): string
    {
        $thumbnail = clone $image;
        $thumbnail->fit(
            $this->maxDimensions['thumbnail']['width'],
            $this->maxDimensions['thumbnail']['height']
        );
        
        $thumbnailFilename = 'thumb_' . uniqid() . '_' . time() . '.jpg';
        $thumbnailPath = 'media/thumbnails/' . date('Y/m') . '/' . $thumbnailFilename;
        
        $encoded = $thumbnail->encode('jpg', 70);
        Storage::disk($disk)->put($thumbnailPath, $encoded);
        
        return $thumbnailPath;
    }

    /**
     * Store file without optimization (for non-images)
     */
    protected function storeWithoutOptimization(UploadedFile $file, string $disk): array
    {
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $path = 'media/' . date('Y/m') . '/' . $filename;
        
        Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));
        
        return [
            'file_path' => $path,
            'thumbnail_path' => null,
            'size' => $file->getSize(),
            'optimized_size' => $file->getSize(),
            'width' => null,
            'height' => null,
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
        ];
    }

    /**
     * Check if file is an image
     */
    protected function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    /**
     * Optimize for specific context (logo, favicon, etc.)
     */
    public function optimizeForContext(
        UploadedFile $file,
        string $context,
        ?string $disk = 'public'
    ): array {
        return $this->optimizeUploadedFile($file, $context, $disk);
    }

    /**
     * Get supported image formats
     */
    public function getSupportedFormats(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
    }

    /**
     * Get maximum file size in bytes
     */
    public function getMaxFileSize(): int
    {
        return 10 * 1024 * 1024; // 10MB
    }
}
