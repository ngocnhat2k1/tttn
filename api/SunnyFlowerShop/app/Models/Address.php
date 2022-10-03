<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public function customers() {
        return $this->belongsToMany(Customer::class, "address_customer", "customer_id", "address_id");
    }
}
