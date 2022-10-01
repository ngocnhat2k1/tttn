<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function customers() {
        return $this->belongsTo(Customer::class);
    }

    public function products() {
        return $this->belongsToMany(Product::class);
    }

    public function vouchers() {
        return $this->belongsTo(Voucher::class);
    }
}
