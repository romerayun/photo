<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Series extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'slug',
        'title_ru',
        'title_en',
        'description_ru',
        'description_en',
        'location_ru',
        'location_en',
        'shooting_date',
        'cover_image',
        'is_featured',
        'is_published',
        'is_demo',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'is_demo' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('sort_order');
    }

    public function scopeVisible(Builder $query, bool $includeDemo = true): Builder
    {
        $query->where('is_published', true);
        if (!$includeDemo) {
            $query->where('is_demo', false);
        }
        return $query;
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'en' && !empty($this->title_en)) {
            return $this->title_en;
        }
        return $this->title_ru ?: '';
    }

    public function localizedDescription(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description_ru;
    }

    public function localizedLocation(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        if ($locale === 'en' && !empty($this->location_en)) {
            return $this->location_en;
        }
        return $this->location_ru;
    }

    public function hasLocaleTranslation(string $locale): bool
    {
        if ($locale === 'en') {
            return !empty($this->title_en);
        }
        return !empty($this->title_ru);
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) {
            if (str_starts_with($this->cover_image, 'http') || str_starts_with($this->cover_image, '/')) {
                return $this->cover_image;
            }
            return asset('storage/' . $this->cover_image);
        }

        // Fallback to first photo
        $firstPhoto = $this->photos()->first();
        if ($firstPhoto) {
            return $firstPhoto->url;
        }

        return asset('images/placeholder.jpg');
    }
}
