<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public function addresses() {
        return $this->belongsToMany(Address::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function customer_product_feedback() {
        return $this->belongsToMany(Product::class, "customer_product_feedback");
    }

    public function customer_product_favorite() {
        return $this->belongsToMany(Product::class, "customer_product_favorite");
    }

    public function customer_product_cart() {
        return $this->belongsToMany(Product::class, "customer_product_cart");
    }
}
