<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_name",
        "email",
        "password",
        "avatar"
    ];

    protected $table = "admins";

    public function admin_token() {
        return $this->hasMany(AdminToken::class);
    }
}