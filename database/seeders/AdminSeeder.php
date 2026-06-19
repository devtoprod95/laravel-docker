<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    # php artisan db:seed --class=AdminSeeder
    public function run(): void
    {
        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('1234'),
            'name'     => '관리자',
        ]);

        Admin::create([
            'username' => 'tester',
            'password' => Hash::make('1234'),
            'name'     => '테스트사용자',
        ]);
    }
}
