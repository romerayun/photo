<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_id',
        'image_path',
        'alt_ru',
        'alt_en',
        'caption_ru',
        'caption_en',
        'width',
        'height',
        'sort_order',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'sort_order' => 'integer',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function localizedAlt(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'en' && !empty($this->alt_en)) {
            return $this->alt_en;
        }
        return $this->alt_ru ?? '';
    }

    public function localizedCaption(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'en' && !empty($this->caption_en)) {
            return $this->caption_en;
        }
        return $this->caption_ru;
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http') || str_starts_with($this->image_path, '/')) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }

    /**
     * Get URL for a specific variant (thumb, md, or original) and format (avif, webp, original).
     */
    public function getVariantUrl(string $size = 'original', ?string $format = null): string
    {
        if (str_starts_with($this->image_path, 'http') || str_starts_with($this->image_path, '/')) {
            return $this->image_path;
        }

        $pathInfo = pathinfo($this->image_path);
        $dirname = $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '';
        $filename = $pathInfo['filename'];
        $ext = $pathInfo['extension'] ?? 'jpg';

        $suffix = match($size) {
            'thumb' => '-thumb',
            'md' => '-md',
            default => '',
        };

        $targetExt = $format ?: $ext;
        $targetPath = "{$dirname}{$filename}{$suffix}.{$targetExt}";

        // If file exists on public disk, use it; otherwise fallback to primary url
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($targetPath)) {
            return asset('storage/' . $targetPath);
        }

        // If specific format doesn't exist, check standard extension
        if ($format && \Illuminate\Support\Facades\Storage::disk('public')->exists("{$dirname}{$filename}{$suffix}.{$ext}")) {
            return asset('storage/' . "{$dirname}{$filename}{$suffix}.{$ext}");
        }

        return $this->url;
    }

    public function getMediumUrlAttribute(): string
    {
        return $this->getVariantUrl('md');
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->getVariantUrl('thumb');
    }

    /**
     * Generate responsive srcset string for a given format.
     */
    public function getSrcsetAttribute(?string $format = null): string
    {
        $thumb = $this->getVariantUrl('thumb', $format);
        $md = $this->getVariantUrl('md', $format);
        $full = $this->getVariantUrl('original', $format);

        return "{$thumb} 600w, {$md} 1200w, {$full} 2560w";
    }
}

