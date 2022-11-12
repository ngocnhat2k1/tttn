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
        "quantity",
        "status",
    ];

    public function categories() {
        return $this->belongsToMany(Category::class, "category_product", "product_id", "category_id");
    }

    public function orders() {
        return $this->belongsToMany(Order::class, "order_product", "order_id", "product_id")->withPivot("quantity", "price", "percent_sale");
    }

    public function customer_product_feedback() {
        return $this->belongsToMany(Customer::class, "customer_product_feedback", "customer_id", "product_id")->withPivot("id", "quality", "comment");
    }

    public function customer_product_favorite() {
        return $this->belongsToMany(Customer::class, "customer_product_favorite", "customer_id", "product_id");
    }

    public function customer_product_cart() {
        return $this->belongsToMany(Customer::class, "customer_product_cart", "customer_id", "product_id")->withPivot("quantity");
    }
}
