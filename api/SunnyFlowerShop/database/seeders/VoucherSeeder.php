<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Voucher::factory()
        ->count(6)
        ->hasOrders(3)
        ->create();
        // Voucher::factory()
        // ->count(10)
        // ->create();
    }
}
