<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset('storage/' . ltrim($this->image_path, '/'));
    }

    public function getVariantUrl(string $size = 'original', ?string $format = null): string
    {
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        $imagePath = ltrim($this->image_path, '/');
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

    public function getMediumUrlAttribute(): string
    {
        return $this->getVariantUrl('md');
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->getVariantUrl('thumb');
    }

    public function getSrcsetAttribute(?string $format = null): string
    {
        $thumb = $this->getVariantUrl('thumb', $format);
        $md = $this->getVariantUrl('md', $format);
        $full = $this->getVariantUrl('original', $format);

        return "{$thumb} 600w, {$md} 1200w, {$full} 2560w";
    }
}

