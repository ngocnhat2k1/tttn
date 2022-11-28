<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Momo extends Model
{
    use HasFactory;

    protected $fillable = [
        "order_id",
        "partner_code",
        "order_type",
        "trans_id",
        "pay_type",
        "status",
        "signature"
    ];

    protected $table = "momo";
}
