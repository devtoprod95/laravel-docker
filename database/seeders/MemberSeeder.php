<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    # php artisan db:seed --class=MemberSeeder
    public function run(): void
    {
        Member::create([
            'username' => 'admin',
            'password' => Hash::make('1234'),
            'name'     => '관리자',
            'role'     => 'admin',
        ]);

        Member::create([
            'username' => 'tester',
            'password' => Hash::make('1234'),
            'name'     => '테스트사용자',
            'role'     => 'user',
        ]);
    }
}
