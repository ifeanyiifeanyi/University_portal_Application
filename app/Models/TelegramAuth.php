<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramAuth extends Model
{
    protected $fillable = [
        'user_id', 'telegram_id', 'token', 'is_active', 'expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
