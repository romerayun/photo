<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Max resolution on the longest side for high-res portfolio photos.
     * 2560px provides crystal clear Retina/4K fidelity while keeping payload small.
     */
    public const MAX_SERIES_DIMENSION = 2560;

    /**
     * Medium variant for tablet screens, modal dialogs, and large article headers.
     */
    public const MEDIUM_DIMENSION = 1200;

    /**
     * Thumbnail variant for mobile screens, masonry cards, and preview grids.
     */
    public const THUMBNAIL_DIMENSION = 600;

    /**
     * Max resolution on the longest side for category cover photos.
     */
    public const MAX_CATEGORY_DIMENSION = 1920;

    /**
     * Target JPEG quality for web (recommended by Google PageSpeed/Lighthouse: 80–85).
     */
    public const JPEG_QUALITY = 83;

    /**
     * Target WebP quality (cwebp).
     */
    public const WEBP_QUALITY = 82;

    /**
     * Target AVIF quality (avifenc).
     */
    public const AVIF_QUALITY = 65;

    /**
     * Optimize an uploaded image for SEO and store it on the public disk.
     * Generates multiple responsive sizes (full, medium, thumbnail)
     * as well as modern formats (WebP and AVIF).
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

        // Generate SEO-friendly filename (base name without extension)
        $fileName = $this->generateSeoFilename($file, $semanticSlug, $mime);
        $relativePath = trim($directory, '/') . '/' . $fileName;

        // Attempt optimization via GD
        $optimized = $this->processWithGd($sourcePath, $mime, $maxDimension);

        if ($optimized !== null) {
            Storage::disk('public')->put($relativePath, $optimized['contents']);

            $fullPath = Storage::disk('public')->path($relativePath);
            $this->generateVariantsAndModernFormats($fullPath, $optimized['width'], $optimized['height']);

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
        $width = $dimensions ? $dimensions[0] : null;
        $height = $dimensions ? $dimensions[1] : null;

        if ($width && $height) {
            $this->generateVariantsAndModernFormats($fullPath, $width, $height);
        }

        return [
            'path' => $storedPath,
            'width' => $width,
            'height' => $height,
            'size' => file_exists($fullPath) ? (int) filesize($fullPath) : (int) $file->getSize(),
        ];
    }

    /**
     * Generate responsive sizes (medium, thumbnail) and WebP/AVIF versions for an image file.
     *
     * @param string $sourceFullPath Absolute local path to the full image
     * @param int $origW
     * @param int $origH
     * @return void
     */
    public function generateVariantsAndModernFormats(string $sourceFullPath, int $origW, int $origH): void
    {
        if (!file_exists($sourceFullPath)) {
            return;
        }

        $pathInfo = pathinfo($sourceFullPath);
        $dirname = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $ext = strtolower($pathInfo['extension'] ?? 'jpg');

        // Target paths
        $mdFullPath = "{$dirname}/{$filename}-md.{$ext}";
        $thumbFullPath = "{$dirname}/{$filename}-thumb.{$ext}";

        // 1. Generate Medium variant (1200px)
        if ($origW > self::MEDIUM_DIMENSION || $origH > self::MEDIUM_DIMENSION) {
            $this->resizeImageFile($sourceFullPath, $mdFullPath, self::MEDIUM_DIMENSION);
        } else {
            @copy($sourceFullPath, $mdFullPath);
        }

        // 2. Generate Thumbnail variant (600px)
        if ($origW > self::THUMBNAIL_DIMENSION || $origH > self::THUMBNAIL_DIMENSION) {
            $this->resizeImageFile($sourceFullPath, $thumbFullPath, self::THUMBNAIL_DIMENSION);
        } else {
            @copy($sourceFullPath, $thumbFullPath);
        }

        // 3. Convert all sizes to WebP and AVIF
        $allFiles = [
            $sourceFullPath,
            $mdFullPath,
            $thumbFullPath,
        ];

        foreach ($allFiles as $file) {
            if (file_exists($file)) {
                $base = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME);
                $this->convertToWebp($file, "{$base}.webp");
                $this->convertToAvif($file, "{$base}.avif");
            }
        }
    }

    /**
     * Convert an image file to WebP format.
     */
    public function convertToWebp(string $sourcePath, string $targetPath): bool
    {
        // 1. Try CLI cwebp if available
        $cwebpBinary = $this->findBinary(['/opt/homebrew/bin/cwebp', '/Applications/XAMPP/xamppfiles/bin/cwebp', 'cwebp']);
        if ($cwebpBinary) {
            $escapedSource = escapeshellarg($sourcePath);
            $escapedTarget = escapeshellarg($targetPath);
            $q = self::WEBP_QUALITY;
            exec("{$cwebpBinary} -q {$q} {$escapedSource} -o {$escapedTarget} 2>&1", $output, $returnCode);
            if ($returnCode === 0 && file_exists($targetPath) && filesize($targetPath) > 0) {
                return true;
            }
        }

        // 2. Fallback to PHP GD if WebP is supported
        if (function_exists('imagewebp') && extension_loaded('gd')) {
            $img = $this->createGdImageFromFile($sourcePath);
            if ($img) {
                imagewebp($img, $targetPath, self::WEBP_QUALITY);
                imagedestroy($img);
                return file_exists($targetPath) && filesize($targetPath) > 0;
            }
        }

        return false;
    }

    /**
     * Convert an image file to AVIF format.
     */
    public function convertToAvif(string $sourcePath, string $targetPath): bool
    {
        // 1. Try CLI avifenc if available
        $avifencBinary = $this->findBinary(['/opt/homebrew/bin/avifenc', 'avifenc']);
        if ($avifencBinary) {
            $escapedSource = escapeshellarg($sourcePath);
            $escapedTarget = escapeshellarg($targetPath);
            $q = self::AVIF_QUALITY;
            // speed 8 ensures fast encoding (~30ms) while keeping high quality
            exec("{$avifencBinary} -s 8 -q {$q} {$escapedSource} {$escapedTarget} 2>&1", $output, $returnCode);
            if ($returnCode === 0 && file_exists($targetPath) && filesize($targetPath) > 0) {
                return true;
            }
        }

        // 2. Fallback to PHP GD if imageavif is supported
        if (function_exists('imageavif') && extension_loaded('gd')) {
            $img = $this->createGdImageFromFile($sourcePath);
            if ($img) {
                imageavif($img, $targetPath, self::AVIF_QUALITY);
                imagedestroy($img);
                return file_exists($targetPath) && filesize($targetPath) > 0;
            }
        }

        return false;
    }

    /**
     * Resize an image file on disk to a target max dimension using GD.
     */
    protected function resizeImageFile(string $sourcePath, string $destPath, int $maxDimension): bool
    {
        if (!extension_loaded('gd') || !file_exists($sourcePath)) {
            return false;
        }

        $image = $this->createGdImageFromFile($sourcePath);
        if (!$image) {
            return false;
        }

        $origW = imagesx($image);
        $origH = imagesy($image);

        if ($origW <= 0 || $origH <= 0) {
            imagedestroy($image);
            return false;
        }

        $ratio = min($maxDimension / $origW, $maxDimension / $origH);
        $targetW = max(1, (int) round($origW * $ratio));
        $targetH = max(1, (int) round($origH * $ratio));

        $resized = imagecreatetruecolor($targetW, $targetH);

        // Preserve transparency for PNG
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);
        imagedestroy($image);

        imageinterlace($resized, true);
        imagejpeg($resized, $destPath, self::JPEG_QUALITY);
        imagedestroy($resized);

        return file_exists($destPath);
    }

    /**
     * Create GD image resource from file.
     */
    protected function createGdImageFromFile(string $filePath)
    {
        $mime = mime_content_type($filePath);

        if (in_array($mime, ['image/jpeg', 'image/jpg', 'image/pjpeg'])) {
            return @imagecreatefromjpeg($filePath);
        } elseif ($mime === 'image/png') {
            return @imagecreatefrompng($filePath);
        } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            return @imagecreatefromwebp($filePath);
        }

        $contents = @file_get_contents($filePath);
        if ($contents) {
            return @imagecreatefromstring($contents);
        }

        return null;
    }

    /**
     * Find binary in provided list or via `which`.
     */
    protected function findBinary(array $candidates): ?string
    {
        foreach ($candidates as $cand) {
            if (str_starts_with($cand, '/')) {
                if (file_exists($cand) && is_executable($cand)) {
                    return $cand;
                }
            } else {
                $check = trim((string) @shell_exec("which " . escapeshellarg($cand) . " 2>/dev/null"));
                if (!empty($check) && file_exists($check) && is_executable($check)) {
                    return $check;
                }
            }
        }
        return null;
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
