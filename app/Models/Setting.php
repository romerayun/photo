<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    public static function isDemoMode(): bool
    {
        return (bool) static::get('demo_mode', true);
    }

    public static function hasTelegram(): bool
    {
        $val = trim((string) static::get('telegram', ''));
        return !empty($val);
    }

    public static function hasPhone(): bool
    {
        $val = trim((string) static::get('phone', ''));
        return !empty($val);
    }

    public static function telegramUrl(): ?string
    {
        $val = trim((string) static::get('telegram', ''));
        if (empty($val)) {
            return null;
        }

        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        $username = ltrim($val, '@');
        return 'https://t.me/' . $username;
    }

    public static function telegramHandle(): ?string
    {
        $val = trim((string) static::get('telegram', ''));
        if (empty($val)) {
            return null;
        }

        if (str_starts_with($val, 'https://t.me/')) {
            return '@' . substr($val, 13);
        }

        return str_starts_with($val, '@') ? $val : '@' . $val;
    }

    public static function phoneLink(): ?string
    {
        $val = trim((string) static::get('phone', ''));
        if (empty($val)) {
            return null;
        }

        $clean = preg_replace('/[^\d+]/', '', $val);
        return 'tel:' . $clean;
    }

    public static function phoneDisplay(): ?string
    {
        $val = trim((string) static::get('phone', ''));
        return !empty($val) ? $val : null;
    }
}
