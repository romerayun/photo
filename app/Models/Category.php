<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_ru',
        'name_en',
        'description_ru',
        'description_en',
        'content',
        'content_en',
        'meta_title',
        'meta_description',
        'image',
        'sort_order',
    ];

    public function series(): HasMany
    {
        return $this->hasMany(Series::class)->orderBy('sort_order');
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'en' && !empty($this->name_en)) {
            return $this->name_en;
        }
        return $this->name_ru ?? '';
    }

    public function localizedDescription(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description_ru;
    }

    public function localizedContent(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'en' && !empty($this->content_en)) {
            return $this->content_en;
        }
        return $this->content;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http') || str_starts_with($this->image, '/')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        // Demo fallback based on slug
        $sampleImg = match($this->slug) {
            'portraits' => 'storage/demo/portrait-1.jpg',
            'couples' => 'storage/demo/couple-1.jpg',
            'families' => 'storage/demo/family-1.jpg',
            'events' => 'storage/demo/event-1.jpg',
            default => 'storage/demo/business-1.jpg',
        };

        return asset($sampleImg);
    }

    public function getVariantImageUrl(string $size = 'original', ?string $format = null): string
    {
        $imagePath = $this->image;
        if (empty($imagePath)) {
            $imagePath = match($this->slug) {
                'portraits' => 'demo/portrait-1.jpg',
                'couples' => 'demo/couple-1.jpg',
                'families' => 'demo/family-1.jpg',
                'events' => 'demo/event-1.jpg',
                default => 'demo/business-1.jpg',
            };
        }

        if (str_starts_with($imagePath, 'http') || str_starts_with($imagePath, '/')) {
            return $imagePath;
        }

        $imagePath = ltrim($imagePath, '/');
        $pathInfo = pathinfo($imagePath);
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

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($targetPath)) {
            return asset('storage/' . $targetPath);
        }

        if ($format && \Illuminate\Support\Facades\Storage::disk('public')->exists("{$dirname}{$filename}{$suffix}.{$ext}")) {
            return asset('storage/' . "{$dirname}{$filename}{$suffix}.{$ext}");
        }

        return $this->image_url;
    }

    public function getMediumImageUrlAttribute(): string
    {
        return $this->getVariantImageUrl('md');
    }

    public function getThumbnailImageUrlAttribute(): string
    {
        return $this->getVariantImageUrl('thumb');
    }

    public function getImageSrcsetAttribute(?string $format = null): string
    {
        $thumb = $this->getVariantImageUrl('thumb', $format);
        $md = $this->getVariantImageUrl('md', $format);
        $full = $this->getVariantImageUrl('original', $format);

        return "{$thumb} 600w, {$md} 1200w, {$full} 1920w";
    }
}

