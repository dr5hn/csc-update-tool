<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagicLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'payload',
        'ip_address',
        'user_agent',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];
}