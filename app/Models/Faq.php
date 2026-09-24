<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_ru',
        'question_en',
        'answer_ru',
        'answer_en',
        'is_draft',
        'sort_order',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_draft', false)->whereNotNull('answer_ru')->orderBy('sort_order');
    }

    public function localizedQuestion(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        return ($locale === 'en' && !empty($this->question_en)) ? $this->question_en : ($this->question_ru ?? '');
    }

    public function localizedAnswer(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        return ($locale === 'en' && !empty($this->answer_en)) ? $this->answer_en : $this->answer_ru;
    }
}
