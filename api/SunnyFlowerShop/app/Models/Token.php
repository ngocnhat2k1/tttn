<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $table = "tokens";

    protected $fillable = [
        "customer_id",
        "token",
        "created_at",
        "updated_at"
    ];

    public function customers() {
        return $this->belongsTo(Customer::class);
    }
}
