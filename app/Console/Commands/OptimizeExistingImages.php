<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize-existing {--force : Re-optimize already processed images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate WebP, AVIF, and responsive thumbnails/medium sizes for existing images in storage/app/public';

    /**
     * Execute the console command.
     */
    public function handle(ImageOptimizer $optimizer): int
    {
        $storageDir = storage_path('app/public');
        if (!is_dir($storageDir)) {
            $this->error("Storage public directory not found: {$storageDir}");
            return 1;
        }

        $this->info("Scanning images in {$storageDir}...");

        $files = File::allFiles($storageDir);
        $count = 0;
        $processed = 0;

        foreach ($files as $file) {
            $pathname = $file->getPathname();
            $filename = $file->getFilename();
            $ext = strtolower($file->getExtension());

            // Only process original JPEG and PNG files (skip already generated variants)
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                continue;
            }

            if (str_ends_with($filename, '-thumb.' . $ext) || str_ends_with($filename, '-md.' . $ext)) {
                continue;
            }

            $count++;
            $basePath = pathinfo($pathname, PATHINFO_DIRNAME) . '/' . pathinfo($pathname, PATHINFO_FILENAME);
            $thumbPath = "{$basePath}-thumb.{$ext}";
            $webpPath = "{$basePath}.webp";
            $avifPath = "{$basePath}.avif";

            $needsProcessing = $this->option('force') || !file_exists($thumbPath) || !file_exists($webpPath) || !file_exists($avifPath);

            if ($needsProcessing) {
                $this->line("Processing: " . $file->getRelativePathname());
                $dimensions = @getimagesize($pathname);
                $w = $dimensions ? $dimensions[0] : 1920;
                $h = $dimensions ? $dimensions[1] : 1080;

                $optimizer->generateVariantsAndModernFormats($pathname, $w, $h);
                $processed++;
            }
        }

        $this->info("Done! Processed {$processed} of {$count} images.");

        return 0;
    }
}
