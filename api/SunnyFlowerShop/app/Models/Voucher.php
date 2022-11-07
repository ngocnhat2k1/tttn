<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "percent",
        "usage",
        "expired_date",
    ];

    public function orders() {
        return $this->hasMany(Order::class);
    }
}
