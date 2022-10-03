<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'percent_sale',
        'img',
        'noteable',
        "quantity",
        "status",
        'deleted_at',
    ];

    public function categories() {
        return $this->belongsToMany(Category::class, "category_product", "category_id", "product_id");
    }

    public function orders() {
        return $this->belongsToMany(Order::class);
    }

    public function customer_product_feedback() {
        return $this->belongsToMany(Customer::class, "customer_product_feedback", "customer_id", "product_id")->withPivot("quality", "comment");
    }

    public function customer_product_favorite() {
        return $this->belongsToMany(Customer::class, "customer_product_favorite", "customer_id", "product_id");
    }

    public function customer_product_cart() {
        return $this->belongsToMany(Customer::class, "customer_product_cart", "customer_id", "product_id")->withPivot("quantity");
    }
}
