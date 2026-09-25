<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'reading_time',
        'views_count',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'reading_time' => 'integer',
        'views_count' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Only approved comments for public display.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->where('is_approved', true)
            ->latest();
    }

    /**
     * All comments for admin moderation.
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Additional photos/gallery attached to the article.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ArticleImage::class)->orderBy('sort_order');
    }

    /**
     * Scope for published articles.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Get the full URL to the cover image.
     */
    public function getCoverUrlAttribute(): string
    {
        if (empty($this->cover_image)) {
            return asset('storage/demo/hero-main.jpg');
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        return asset('storage/' . ltrim($this->cover_image, '/'));
    }

    /**
     * Calculate reading time in minutes based on Russian text word count.
     */
    public function getEstimatedReadingTimeAttribute(): int
    {
        if ($this->reading_time && $this->reading_time > 0) {
            return $this->reading_time;
        }

        $cleanText = strip_tags($this->content ?? '');
        $wordCount = count(preg_split('/\s+/u', trim($cleanText), -1, PREG_SPLIT_NO_EMPTY));
        $minutes = (int) ceil($wordCount / 180);

        return max(1, $minutes);
    }

    /**
     * Formatted date in Russian (e.g. "24 сентября 2026").
     */
    public function getFormattedDateAttribute(): string
    {
        $date = $this->published_at ?? $this->created_at ?? now();
        return Carbon::parse($date)->locale('ru')->isoFormat('D MMMM YYYY');
    }
}
