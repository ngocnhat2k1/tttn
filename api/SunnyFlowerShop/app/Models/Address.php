<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        "name_receiver",
        "phone_receiver",
        "street_name",
        "district",
        "ward",
        "city"
    ];

    public function customers() {
        return $this->belongsToMany(Customer::class, "address_customer", "customer_id", "address_id")->withPivot("id");
    }
}
