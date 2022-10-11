<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public function addresses() {
        return $this->belongsToMany(Address::class, "address_customer", "customer_id", "address_id")->withPivot("id");
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }
    
    public function customer_product_feedback() {
        return $this->belongsToMany(Product::class, "customer_product_feedback", "customer_id", "product_id")->withPivot("id","quality", "comment");
    }

    public function customer_product_favorite() {
        return $this->belongsToMany(Product::class, "customer_product_favorite", "customer_id", "product_id");
    }

    public function customer_product_cart() {
        return $this->belongsToMany(Product::class, "customer_product_cart", "customer_id", "product_id")->withPivot("quantity");
    }
}
