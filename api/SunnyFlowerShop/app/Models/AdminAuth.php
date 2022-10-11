<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\CustomAuth\AdminAuth as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AdminAuth extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        "username",
        "email",
        "password",
        "level",

        // Temporary
        'token',
    ];

    protected $table = "admins";
}
