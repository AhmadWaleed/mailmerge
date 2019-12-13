<?php

namespace Mailmerge;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected array $fillable = [
        'name', 'email', 'password',
    ];

    protected array $hidden = [
        'password', 'remember_token',
    ];

    protected array $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function validApiKey(string $apiKey): bool
    {
        return static::query()->where('api_key', $apiKey)->exists();
    }
}