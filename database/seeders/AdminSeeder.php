<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@amycell.id'],
            [
                'name'     => 'Admin Amycell',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'phone'    => '081234567890',
                'address'  => 'Jl. Sudirman No. 1, Jakarta Pusat',
            ]
        );

        // Demo customer
        User::firstOrCreate(
            ['email' => 'pelanggan@amycell.id'],
            [
                'name'     => 'Budi Santoso',
                'password' => Hash::make('pelanggan123'),
                'role'     => 'customer',
                'phone'    => '085678901234',
                'address'  => 'Jl. Gatot Subroto No. 10, Jakarta Selatan',
            ]
        );
    }
}
