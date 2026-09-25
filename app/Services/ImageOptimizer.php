<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Max resolution on the longest side for series portfolio photos.
     * 2560px provides crystal clear Retina/4K fidelity while keeping payload small.
     */
    public const MAX_SERIES_DIMENSION = 2560;

    /**
     * Max resolution on the longest side for category cover photos.
     */
    public const MAX_CATEGORY_DIMENSION = 1920;

    /**
     * Target JPEG quality for web (recommended by Google PageSpeed/Lighthouse: 80–85).
     */
    public const JPEG_QUALITY = 83;

    /**
     * Optimize an uploaded image for SEO and store it on the public disk.
     *
     * @param UploadedFile $file
     * @param string $directory Storage subdirectory, e.g. "series/1" or "categories"
     * @param string|null $semanticSlug Keywords / title slug for semantic filename SEO
     * @param int $maxDimension Max width/height on the longest side
     * @return array{path: string, width: int|null, height: int|null, size: int}
     */
    public function optimizeAndStore(
        UploadedFile $file,
        string $directory,
        ?string $semanticSlug = null,
        int $maxDimension = self::MAX_SERIES_DIMENSION
    ): array {
        $sourcePath = $file->getRealPath() ?: $file->getPathname();
        $mime = strtolower((string) $file->getMimeType());
        $ext = strtolower((string) $file->getClientOriginalExtension());

        // Generate SEO-friendly filename
        $fileName = $this->generateSeoFilename($file, $semanticSlug, $mime);
        $relativePath = trim($directory, '/') . '/' . $fileName;

        // Attempt optimization via GD
        $optimized = $this->processWithGd($sourcePath, $mime, $maxDimension);

        if ($optimized !== null) {
            Storage::disk('public')->put($relativePath, $optimized['contents']);
            return [
                'path' => $relativePath,
                'width' => $optimized['width'],
                'height' => $optimized['height'],
                'size' => strlen($optimized['contents']),
            ];
        }

        // Fallback: store original file directly if GD cannot process this format
        $storedPath = $file->storeAs($directory, $fileName, 'public');
        $fullPath = Storage::disk('public')->path($storedPath);
        $dimensions = @getimagesize($fullPath);

        return [
            'path' => $storedPath,
            'width' => $dimensions ? $dimensions[0] : null,
            'height' => $dimensions ? $dimensions[1] : null,
            'size' => file_exists($fullPath) ? (int) filesize($fullPath) : (int) $file->getSize(),
        ];
    }

    /**
     * Process image with GD: auto-orient, downsample to max dimension, make progressive JPEG.
     *
     * @param string $filePath
     * @param string $mime
     * @param int $maxDimension
     * @return array{contents: string, width: int, height: int}|null
     */
    protected function processWithGd(string $filePath, string $mime, int $maxDimension): ?array
    {
        if (!extension_loaded('gd') || !file_exists($filePath)) {
            return null;
        }

        $image = null;
        $isPngWithAlpha = false;

        if (in_array($mime, ['image/jpeg', 'image/jpg', 'image/pjpeg'])) {
            $image = @imagecreatefromjpeg($filePath);
        } elseif ($mime === 'image/png') {
            $image = @imagecreatefrompng($filePath);
            $isPngWithAlpha = true;
        } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $image = @imagecreatefromwebp($filePath);
        } else {
            // Try generic fallback from string
            $contents = @file_get_contents($filePath);
            if ($contents) {
                $image = @imagecreatefromstring($contents);
            }
        }

        if (!$image) {
            return null;
        }

        // 1. Auto-orient based on camera EXIF tags
        $image = $this->autoOrientExif($image, $filePath, $mime);

        $origW = imagesx($image);
        $origH = imagesy($image);

        if ($origW <= 0 || $origH <= 0) {
            imagedestroy($image);
            return null;
        }

        // 2. Calculate proportional dimensions (no upscaling)
        $targetW = $origW;
        $targetH = $origH;

        if ($origW > $maxDimension || $origH > $maxDimension) {
            $ratio = min($maxDimension / $origW, $maxDimension / $origH);
            $targetW = max(1, (int) round($origW * $ratio));
            $targetH = max(1, (int) round($origH * $ratio));
        }

        // 3. Resample if dimensions changed
        if ($targetW !== $origW || $targetH !== $origH) {
            $resized = imagecreatetruecolor($targetW, $targetH);

            if ($isPngWithAlpha) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefilledrectangle($resized, 0, 0, $targetW, $targetH, $transparent);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);
            imagedestroy($image);
            $image = $resized;
        }

        // 4. Encode to memory buffer
        ob_start();

        if ($isPngWithAlpha) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, null, 8); // PNG compression 0-9
        } else {
            // Enable Progressive JPEG for faster initial render & LCP boost
            imageinterlace($image, true);
            imagejpeg($image, null, self::JPEG_QUALITY);
        }

        $buffer = (string) ob_get_clean();
        imagedestroy($image);

        if (empty($buffer)) {
            return null;
        }

        return [
            'contents' => $buffer,
            'width' => $targetW,
            'height' => $targetH,
        ];
    }

    /**
     * Rotate image according to EXIF Orientation tag.
     *
     * @param \GdImage|resource $image
     * @param string $filePath
     * @param string $mime
     * @return \GdImage|resource
     */
    protected function autoOrientExif($image, string $filePath, string $mime)
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        if (!in_array($mime, ['image/jpeg', 'image/jpg', 'image/tiff'])) {
            return $image;
        }

        $exif = @exif_read_data($filePath);
        if (empty($exif['Orientation'])) {
            return $image;
        }

        $orientation = (int) $exif['Orientation'];

        switch ($orientation) {
            case 3: // 180 degrees
                $rotated = imagerotate($image, 180, 0);
                if ($rotated) {
                    imagedestroy($image);
                    return $rotated;
                }
                break;

            case 6: // 90 degrees clockwise (needs 270 deg rotation)
                $rotated = imagerotate($image, -90, 0);
                if ($rotated) {
                    imagedestroy($image);
                    return $rotated;
                }
                break;

            case 8: // 90 degrees counter-clockwise (needs 90 deg rotation)
                $rotated = imagerotate($image, 90, 0);
                if ($rotated) {
                    imagedestroy($image);
                    return $rotated;
                }
                break;
        }

        return $image;
    }

    /**
     * Generate an SEO-friendly transliterated filename.
     * e.g., "svadebnaya-semka-irkutsk-3f8a1c.jpg"
     */
    protected function generateSeoFilename(UploadedFile $file, ?string $semanticSlug, string $mime): string
    {
        if (!empty($semanticSlug)) {
            $base = Str::slug($semanticSlug);
        } else {
            $clientName = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
            $base = Str::slug($clientName);
        }

        if (empty($base)) {
            $base = 'photo';
        }

        // Keep it concise for URLs and file systems
        $base = Str::limit($base, 45, '');

        $hash = substr(md5(uniqid((string) mt_rand(), true)), 0, 6);

        // Determine extension
        $ext = 'jpg';
        if ($mime === 'image/png') {
            $ext = 'png';
        } elseif ($mime === 'image/webp') {
            $ext = 'webp';
        }

        return "{$base}-{$hash}.{$ext}";
    }
}
