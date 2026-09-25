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
}
