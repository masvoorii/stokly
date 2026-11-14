<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $items = [
            ['name' => 'Laptop Asus Vivobook', 'category' => 'Elektronik', 'stock' => 10, 'price' => 8500000],
            ['name' => 'Mouse Wireless Logitech', 'category' => 'Aksesoris', 'stock' => 25, 'price' => 250000],
            ['name' => 'Keyboard Mechanical', 'category' => 'Aksesoris', 'stock' => 15, 'price' => 550000],
            ['name' => 'Monitor LG 24 Inch', 'category' => 'Elektronik', 'stock' => 8, 'price' => 1800000],
            ['name' => 'Meja Kerja Minimalis', 'category' => 'Furnitur', 'stock' => 5, 'price' => 1200000],
        ];

        foreach ($items as $item) {
            Product::create(array_merge($item, ['user_id' => $user->id]));
        }
    }
}