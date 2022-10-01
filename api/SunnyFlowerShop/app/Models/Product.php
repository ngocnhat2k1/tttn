<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    public function orders() {
        return $this->belongsToMany(Order::class);
    }

    public function customer_product_feedback() {
        return $this->belongsToMany(Customer::class, "customer_product_feedback");
    }

    public function customer_product_favorite() {
        return $this->belongsToMany(Customer::class, "customer_product_favorite");
    }

    public function customer_product_cart() {
        return $this->belongsToMany(Customer::class, "customer_product_cart");
    }
}
