<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'social_link',
        'email',
        'notes',
        'max_user_id',
        'max_chat_id',
        'max_connected_at',
    ];

    protected function casts(): array
    {
        return [
            'max_connected_at' => 'datetime',
        ];
    }

    public function shoots(): HasMany
    {
        return $this->hasMany(Shoot::class)->orderBy('shoot_date', 'desc')->orderBy('start_time', 'desc');
    }

    public function activeShoots(): HasMany
    {
        return $this->hasMany(Shoot::class)
            ->whereIn('status', ['planned', 'in_progress'])
            ->orderBy('shoot_date', 'asc')
            ->orderBy('start_time', 'asc');
    }

    /**
     * Clean phone number without spaces or dashes.
     */
    public function getPhoneCleanAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        return preg_replace('/[^\d+]/', '', $this->phone);
    }
}
