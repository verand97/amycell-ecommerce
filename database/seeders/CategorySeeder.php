<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pulsa', 'slug' => 'pulsa', 'icon' => '📱', 'sort_order' => 1, 'description' => 'Isi pulsa semua operator'],
            ['name' => 'Paket Data', 'slug' => 'paket-data', 'icon' => '📡', 'sort_order' => 2, 'description' => 'Paket internet hemat semua operator'],
            ['name' => 'Token Listrik', 'slug' => 'token-listrik', 'icon' => '⚡', 'sort_order' => 3, 'description' => 'Token PLN prabayar semua nominal'],
            ['name' => 'BPJS', 'slug' => 'bpjs', 'icon' => '🏥', 'sort_order' => 4, 'description' => 'Pembayaran premi BPJS Kesehatan & Ketenagakerjaan'],
            ['name' => 'E-Wallet', 'slug' => 'e-wallet', 'icon' => '💳', 'sort_order' => 5, 'description' => 'Top-up saldo GoPay, OVO, Dana, ShopeePay'],
            ['name' => 'Game Voucher', 'slug' => 'game-voucher', 'icon' => '🎮', 'sort_order' => 6, 'description' => 'Voucher game online Mobile Legends, PUBG, FF'],
            ['name' => 'Streaming', 'slug' => 'streaming', 'icon' => '🎬', 'sort_order' => 7, 'description' => 'Langganan Netflix, Spotify, YouTube Premium'],
            ['name' => 'Aksesori HP', 'slug' => 'aksesori-hp', 'icon' => '🔌', 'sort_order' => 8, 'description' => 'Aksesori dan pelengkap smartphone'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }
    }
}
