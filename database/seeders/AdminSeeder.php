<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            [
                'admin_email' => 'admin@gmail.com',
            ],
            [
                'admin_username' => 'admin',
                'admin_password' => Hash::make('12345678'),
            ]
        );
    }
}
