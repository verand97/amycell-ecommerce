<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Pulsa
            ['cat' => 'pulsa', 'name' => 'Pulsa Telkomsel Rp 10.000', 'price' => 11500, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'pulsa', 'name' => 'Pulsa Telkomsel Rp 25.000', 'price' => 26500, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'pulsa', 'name' => 'Pulsa Telkomsel Rp 50.000', 'price' => 51500, 'type' => 'digital', 'stock' => 999, 'featured' => true],
            ['cat' => 'pulsa', 'name' => 'Pulsa Indosat Rp 25.000', 'price' => 26000, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'pulsa', 'name' => 'Pulsa XL Rp 50.000', 'price' => 51000, 'type' => 'digital', 'stock' => 999, 'featured' => false],

            // Paket Data
            ['cat' => 'paket-data', 'name' => 'Paket Data Telkomsel 2GB 30 Hari', 'price' => 25000, 'sale_price' => 22000, 'type' => 'digital', 'stock' => 999, 'featured' => true],
            ['cat' => 'paket-data', 'name' => 'Paket Data Telkomsel 10GB 30 Hari', 'price' => 65000, 'sale_price' => 59000, 'type' => 'digital', 'stock' => 999, 'featured' => true],
            ['cat' => 'paket-data', 'name' => 'Paket Data Indosat 5GB 30 Hari', 'price' => 35000, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'paket-data', 'name' => 'Paket Data XL 20GB 30 Hari', 'price' => 85000, 'sale_price' => 75000, 'type' => 'digital', 'stock' => 999, 'featured' => false],

            // Token Listrik
            ['cat' => 'token-listrik', 'name' => 'Token PLN Rp 20.000', 'price' => 21500, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'token-listrik', 'name' => 'Token PLN Rp 50.000', 'price' => 51500, 'type' => 'digital', 'stock' => 999, 'featured' => true],
            ['cat' => 'token-listrik', 'name' => 'Token PLN Rp 100.000', 'price' => 101500, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'token-listrik', 'name' => 'Token PLN Rp 200.000', 'price' => 201500, 'type' => 'digital', 'stock' => 999, 'featured' => false],

            // E-Wallet
            ['cat' => 'e-wallet', 'name' => 'Top Up GoPay Rp 50.000', 'price' => 51500, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'e-wallet', 'name' => 'Top Up OVO Rp 100.000', 'price' => 101500, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'e-wallet', 'name' => 'Top Up Dana Rp 50.000', 'price' => 51500, 'type' => 'digital', 'stock' => 999, 'featured' => true],

            // Game Voucher
            ['cat' => 'game-voucher', 'name' => 'Mobile Legends 86 Diamond', 'price' => 20000, 'type' => 'digital', 'stock' => 999, 'featured' => true],
            ['cat' => 'game-voucher', 'name' => 'Mobile Legends 258 Diamond', 'price' => 55000, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'game-voucher', 'name' => 'Free Fire 100 Diamond', 'price' => 18000, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'game-voucher', 'name' => 'PUBG UC 60', 'price' => 15000, 'type' => 'digital', 'stock' => 999, 'featured' => false],

            // Streaming
            ['cat' => 'streaming', 'name' => 'Spotify Premium 1 Bulan', 'price' => 45000, 'sale_price' => 39000, 'type' => 'digital', 'stock' => 999, 'featured' => true],
            ['cat' => 'streaming', 'name' => 'Netflix Basic 1 Bulan', 'price' => 65000, 'type' => 'digital', 'stock' => 999, 'featured' => false],
            ['cat' => 'streaming', 'name' => 'YouTube Premium 1 Bulan', 'price' => 49000, 'type' => 'digital', 'stock' => 999, 'featured' => false],

            // Aksesori HP (Physical)
            ['cat' => 'aksesori-hp', 'name' => 'Kabel Data USB Type-C 1m', 'price' => 35000, 'type' => 'physical', 'stock' => 50, 'featured' => false],
            ['cat' => 'aksesori-hp', 'name' => 'Case HP Anti-Crack Universal', 'price' => 25000, 'type' => 'physical', 'stock' => 100, 'featured' => false],
            ['cat' => 'aksesori-hp', 'name' => 'Power Bank 10000mAh Amycell', 'price' => 150000, 'sale_price' => 125000, 'type' => 'physical', 'stock' => 30, 'featured' => true],
            ['cat' => 'aksesori-hp', 'name' => 'Charger Fast Charging 25W', 'price' => 85000, 'type' => 'physical', 'stock' => 45, 'featured' => false],
        ];

        foreach ($products as $p) {
            $category = Category::where('slug', $p['cat'])->first();
            if (!$category) continue;

            $name = $p['name'];
            $slug = Str::slug($name) . '-' . Str::random(4);

            Product::firstOrCreate(
                ['name' => $name],
                [
                    'category_id'  => $category->id,
                    'slug'         => $slug,
                    'description'  => "Produk {$name} tersedia di Toko Amycell dengan harga terbaik dan proses cepat.",
                    'price'        => $p['price'],
                    'sale_price'   => $p['sale_price'] ?? null,
                    'stock'        => $p['stock'],
                    'type'         => $p['type'],
                    'is_active'    => true,
                    'is_featured'  => $p['featured'],
                    'sold_count'   => rand(10, 500),
                ]
            );
        }
    }
}
