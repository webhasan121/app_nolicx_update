<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmDeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'token_hash',
        'platform',
        'device_id',
        'app_version',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
        ];
    }

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
