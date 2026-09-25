<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Shoot extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'social_link',
        'phone',
        'description',
        'shoot_date',
        'start_time',
        'duration_minutes',
        'status',
        'share_token',
        'location',
        'price',
        'prepayment',
        'receipt_path',
        'receipt_original_name',
        'receipt_uploaded_at',
        'booking_confirmed_at',
        'gallery_link',
        'notes',
        'max_link_code_hash',
        'max_link_code_expires_at',
        'max_user_id',
        'max_chat_id',
        'max_connected_at',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(ShootFile::class);
    }

    protected function casts(): array
    {
        return [
            'shoot_date' => 'date',
            'duration_minutes' => 'integer',
            'price' => 'integer',
            'prepayment' => 'integer',
            'receipt_uploaded_at' => 'datetime',
            'booking_confirmed_at' => 'datetime',
            'max_link_code_expires_at' => 'datetime',
            'max_connected_at' => 'datetime',
        ];
    }

    /**
     * Unique contract/offer number for client.
     */
    public function getContractNumberAttribute(): string
    {
        $year = $this->shoot_date ? $this->shoot_date->format('Y') : date('Y');
        return sprintf('ОФ-%s-%04d', $year, $this->id);
    }

    /**
     * Prepayment amount in rubles. Defaults to 1000 ₽ or configured value.
     */
    public function getPrepaymentAmountAttribute(): int
    {
        if (!is_null($this->prepayment)) {
            return (int) $this->prepayment;
        }

        $totalPrice = $this->price ?? 3500;
        if ($totalPrice <= 1500) {
            return min(500, $totalPrice);
        }

        return 1000;
    }

    /**
     * Remaining balance to pay on shoot day.
     */
    public function getRemainderAmountAttribute(): int
    {
        $total = $this->price ?? 3500;
        return max(0, $total - $this->prepayment_amount);
    }

    /**
     * Prepayment deadline calculation: 1 week before shoot or today if booked within 7 days.
     * All times calculated in Irkutsk timezone (Asia/Irkutsk).
     */
    public function getPrepaymentDeadlineAttribute(): array
    {
        $irkutskTz = 'Asia/Irkutsk';
        $nowIrkutsk = now($irkutskTz);

        $shootTimeStr = substr($this->start_time ?? '12:00', 0, 5);
        $shootDateStr = $this->shoot_date ? $this->shoot_date->format('Y-m-d') : $nowIrkutsk->format('Y-m-d');

        try {
            $shootDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$shootDateStr} {$shootTimeStr}", $irkutskTz);
        } catch (\Exception $e) {
            $shootDateTime = Carbon::parse($shootDateStr, $irkutskTz)->setTime(12, 0);
        }

        $oneWeekBefore = $shootDateTime->copy()->subDays(7)->setTime(21, 0);

        // If one week before has already passed or shoot is within 7 days from now
        if ($oneWeekBefore->isPast() || $nowIrkutsk->diffInDays($shootDateTime, false) <= 7) {
            $deadline = $nowIrkutsk->copy()->setTime(21, 0);
            if ($nowIrkutsk->hour >= 21) {
                $deadline = $nowIrkutsk->copy()->endOfDay();
            }
        } else {
            $deadline = $oneWeekBefore;
        }

        return [
            'datetime' => $deadline,
            'date_formatted' => $deadline->translatedFormat('d F Y'),
            'time_formatted' => $deadline->format('H:i'),
            'formatted' => $deadline->translatedFormat('d F Y') . ' г., до ' . $deadline->format('H:i') . ' по Иркутску',
        ];
    }

    /**
     * Calculate end time based on start_time and duration_minutes.
     */
    public function getEndTimeAttribute(): string
    {
        if (empty($this->start_time)) {
            return '';
        }

        try {
            $time = Carbon::createFromFormat('H:i', substr($this->start_time, 0, 5));
            return $time->addMinutes($this->duration_minutes ?? 60)->format('H:i');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Human readable duration label.
     */
    public function getDurationLabelAttribute(): string
    {
        $minutes = $this->duration_minutes ?? 60;

        return match ($minutes) {
            30 => '30 мин',
            45 => '45 мин',
            60 => '1 час',
            90 => '1.5 часа (90 мин)',
            120 => '2 часа',
            150 => '2.5 часа',
            180 => '3 часа',
            240 => '4 часа',
            default => ($minutes % 60 === 0) ? ($minutes / 60) . ' ч' : "{$minutes} мин",
        };
    }

    /**
     * Status label in Russian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Проведена',
            'cancelled' => 'Отменена',
            default => 'Запланирована',
        };
    }

    /**
     * Normalized social link URL for browser click.
     */
    public function getSocialUrlAttribute(): ?string
    {
        if (empty($this->social_link)) {
            return null;
        }

        $link = trim($this->social_link);

        if (str_starts_with($link, 'http://') || str_starts_with($link, 'https://')) {
            return $link;
        }

        if (str_starts_with($link, '@')) {
            return 'https://t.me/' . substr($link, 1);
        }

        if (str_contains($link, 't.me/') || str_contains($link, 'vk.com/') || str_contains($link, 'instagram.com/')) {
            return 'https://' . ltrim($link, '/');
        }

        // Default: if it's alphanumeric without dots, treat as telegram username or general link
        return 'https://t.me/' . ltrim($link, '@');
    }

    /**
     * Clean phone for tel: links.
     */
    public function getPhoneCleanAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }

        return preg_replace('/[^\d+]/', '', $this->phone);
    }

    /**
     * Normalized gallery URL for client click.
     */
    public function getCleanGalleryUrlAttribute(): ?string
    {
        if (empty($this->gallery_link)) {
            return null;
        }

        $link = trim($this->gallery_link);

        if (str_starts_with($link, 'http://') || str_starts_with($link, 'https://')) {
            return $link;
        }

        return 'https://' . ltrim($link, '/');
    }

    /**
     * Public URL for the uploaded receipt image.
     */
    public function getReceiptUrlAttribute(): ?string
    {
        if (empty($this->receipt_path)) {
            return null;
        }

        return '/storage/' . ltrim($this->receipt_path, '/');
    }

    /**
     * Scope for upcoming shoots.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('shoot_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('shoot_date', 'asc')
            ->orderBy('start_time', 'asc');
    }

    /**
     * Scope for a specific month.
     */
    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('shoot_date', $year)
            ->whereMonth('shoot_date', $month)
            ->orderBy('shoot_date', 'asc')
            ->orderBy('start_time', 'asc');
    }

    public function getShareUrlAttribute(): string
    {
        if (empty($this->share_token)) {
            $this->share_token = Str::random(32);
            $this->saveQuietly();
        }

        return route('shoots.share', ['token' => $this->share_token]);
    }

    protected static function booted(): void
    {
        static::creating(function (Shoot $shoot) {
            if (empty($shoot->share_token)) {
                $shoot->share_token = Str::random(32);
            }
        });

        static::deleting(function (Shoot $shoot) {
            foreach ($shoot->files as $file) {
                $file->delete();
            }
        });
    }
}
