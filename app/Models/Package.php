<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title_ru',
        'title_en',
        'subtitle_ru',
        'subtitle_en',
        'duration_ru',
        'duration_en',
        'photo_count_ru',
        'photo_count_en',
        'includes_ru',
        'includes_en',
        'delivery_time_ru',
        'delivery_time_en',
        'price',
        'is_price_from',
        'extra_conditions_ru',
        'extra_conditions_en',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_price_from' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        return ($locale === 'en' && !empty($this->title_en)) ? $this->title_en : ($this->title_ru ?? '');
    }

    public function localizedSubtitle(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        return ($locale === 'en' && !empty($this->subtitle_en)) ? $this->subtitle_en : $this->subtitle_ru;
    }

    public function localizedDuration(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        return ($locale === 'en' && !empty($this->duration_en)) ? $this->duration_en : $this->duration_ru;
    }

    public function localizedPhotoCount(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        return ($locale === 'en' && !empty($this->photo_count_en)) ? $this->photo_count_en : $this->photo_count_ru;
    }

    public function localizedDeliveryTime(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        return ($locale === 'en' && !empty($this->delivery_time_en)) ? $this->delivery_time_en : $this->delivery_time_ru;
    }

    public function localizedExtraConditions(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        return ($locale === 'en' && !empty($this->extra_conditions_en)) ? $this->extra_conditions_en : $this->extra_conditions_ru;
    }

    /**
     * Return list of includes split by newlines.
     */
    public function getIncludesList(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $text = ($locale === 'en' && !empty($this->includes_en)) ? $this->includes_en : ($this->includes_ru ?? '');
        if (empty(trim($text))) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode("\n", $text))));
    }

    /**
     * Formatted price string according to requirements:
     * Russian: "от 12 000 ₽" or "12 000 ₽" or "Стоимость уточняется"
     * English: "from 12,000 RUB" or "12,000 RUB" or "Price upon request"
     */
    public function formattedPrice(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        if (is_null($this->price)) {
            return $locale === 'en' ? 'Price upon request' : 'Стоимость уточняется';
        }

        if ($locale === 'en') {
            $formatted = number_format($this->price, 0, '.', ',') . ' RUB';
            return $this->is_price_from ? 'from ' . $formatted : $formatted;
        }

        $formatted = number_format($this->price, 0, '.', ' ') . ' ₽';
        return $this->is_price_from ? 'от ' . $formatted : $formatted;
    }
}
