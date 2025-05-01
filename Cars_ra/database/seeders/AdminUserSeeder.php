<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin1@gmail.com'],
            [
                'name' => 'Admin1',
                'password' => Hash::make('password123'), // غير الباسورد بعدين
                'phone' => '000000000',
                'address' => 'location',
                'license_number' => 'ADMIN-000',
                'license_image' => null,
                'role' => 'Admin',
            ]
        );

        $admin->assignRole('Admin');
    }
}
