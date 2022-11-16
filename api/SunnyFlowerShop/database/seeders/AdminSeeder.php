<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      'user_name' => 'Admin',
      'email' => 'admin@email.com',
      'password' => Hash::make('123'),
      'level' => '0',
    ];

    Admin::create($data);

    $data = [
      'user_name' => 'Super Admin',
      'email' => 'sadmin@email.com',
      'password' => Hash::make('123'),
      'level' => '1',
    ];

    Admin::create($data);
  }
}
