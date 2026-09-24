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
}
