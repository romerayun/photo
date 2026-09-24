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
}
