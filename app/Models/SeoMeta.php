<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'title',
        'description',
        'og_title',
        'og_description',
        'og_image',
        'canonical',
        'robots',
        'is_auto_generated',
    ];

    protected $casts = [
        'is_auto_generated' => 'boolean',
    ];

    /**
     * Standardize path to always have leading slash and no trailing slash (except root '/').
     */
    public static function normalizePath(string $path): string
    {
        $parsed = parse_url($path, PHP_URL_PATH);
        $clean = '/' . trim($parsed ?? $path, '/');
        return $clean === '' ? '/' : $clean;
    }

    /**
     * Find SEO record by normalized path.
     */
    public static function findByPath(string $path): ?self
    {
        return static::where('path', static::normalizePath($path))->first();
    }
}
