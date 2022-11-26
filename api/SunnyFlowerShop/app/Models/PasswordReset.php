<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $fillable = [
        "email",
        "token",
        "expired",
        "created_at",
        "updated_at"
    ];

    protected $table = "password_resets";
}
