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

    public static function telegramUrl(?string $text = null): ?string
    {
        $val = trim((string) static::get('telegram', ''));
        if (empty($val)) {
            return null;
        }

        $base = (str_starts_with($val, 'http://') || str_starts_with($val, 'https://'))
            ? $val
            : 'https://t.me/' . ltrim($val, '@');

        if ($text !== null && $text !== '') {
            $separator = str_contains($base, '?') ? '&' : '?';
            return $base . $separator . 'text=' . urlencode($text);
        }

        return $base;
    }

    public static function contactEmail(): string
    {
        $val = trim((string) static::get('contact_email', ''));
        return !empty($val) ? $val : 'romerayun@gmail.com';
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
        $val = trim((string) static::get('phone', '+79148089555'));
        if (empty($val)) {
            $val = '+79148089555';
        }

        $clean = preg_replace('/[^\d+]/', '', $val);
        return 'tel:' . (str_starts_with($clean, '+') ? $clean : '+' . $clean);
    }

    public static function phoneDisplay(): ?string
    {
        $val = trim((string) static::get('phone', '+7 914 808-95-55'));
        if (empty($val)) {
            return '+7 914 808-95-55';
        }

        $clean = preg_replace('/\D/', '', $val);
        if (strlen($clean) === 11 && ($clean[0] === '7' || $clean[0] === '8')) {
            return '+7 ' . substr($clean, 1, 3) . ' ' . substr($clean, 4, 3) . '-' . substr($clean, 7, 2) . '-' . substr($clean, 9, 2);
        }

        return $val;
    }
}
