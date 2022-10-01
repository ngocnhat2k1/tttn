<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public function addresses() {
        return $this->belongsToMany(Address::class, "address_customer", "address_id", "customer_id");
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function customer_product_feedback() {
        return $this->belongsToMany(Product::class, "customer_product_feedback", "product_id", "customer_id")->withPivot("quality", "comment");
    }

    public function customer_product_favorite() {
        return $this->belongsToMany(Product::class, "customer_product_favorite", "product_id", "customer_id");
    }

    public function customer_product_cart() {
        return $this->belongsToMany(Product::class, "customer_product_cart", "product_id", "customer_id")->withPivot("quantity");
    }
}
