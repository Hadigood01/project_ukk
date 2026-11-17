<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // AKUN ADMIN
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama_user' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'level' => 'admin',
            ]
        );

        // AKUN PETUGAS
        User::updateOrCreate(
            ['email' => 'petugas@gmail.com'],
            [
                'nama_user' => 'Petugas',
                'username' => 'petugas',
                'password' => Hash::make('petugas123'),
                'level' => 'petugas',
            ]
        );
    }
}
