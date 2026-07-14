<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * ProductSeeder
 * Seeder untuk membuat data produk sample
 */
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Baju (Category 1)
            [
                'category_id' => 1,
                'name' => 'Long Sleeve T-Shirt STS',
                'price' => 85000,
                'weight' => 500,
                'stock' => 10,
                'description' => 'T-Shirt lengan panjang berkualitas tinggi',
                'image' => 'products/baju/depanbaju1.webp',
            ],
            [
                'category_id' => 1,
                'name' => 'Crew Neck T-Shirt',
                'price' => 100000,
                'weight' => 500,
                'stock' => 10,
                'description' => 'T-Shirt crew neck berkualitas tinggi',
                'image' => 'products/baju/depanbaju2.webp',
            ],
            [
                'category_id' => 1,
                'name' => 'T-Shirt Lengan Pendek',
                'price' => 150000,
                'weight' => 500,
                'stock' => 10,
                'description' => 'T-Shirt lengan pendek berkualitas tinggi',
                'image' => 'products/baju/depanbaju3.webp',
            ],
            [
                'category_id' => 1,
                'name' => 'T-Shirt Kece',
                'price' => 135000,
                'weight' => 500,
                'stock' => 10,
                'description' => 'T-Shirt lengan pendek berkualitas tinggi',
                'image' => 'products/baju/depanbaju4.webp',
            ],
            [
                'category_id' => 1,
                'name' => 'T-Shirt Vintage STS',
                'price' => 145000,
                'weight' => 500,
                'stock' => 10,
                'description' => 'T-Shirt vintage berkualitas tinggi',
                'image' => 'products/baju/depanbaju6.webp',
            ],
            [
                'category_id' => 1,
                'name' => 'Oversized T-Shirt STS',
                'price' => 160000,
                'weight' => 500,
                'stock' => 10,
                'description' => 'T-Shirt oversized fit berkualitas tinggi',
                'image' => 'products/baju/depanbaju7.webp',
            ],
            [
                'category_id' => 1,
                'name' => 'Retro Stripe Tee',
                'price' => 125000,
                'weight' => 500,
                'stock' => 10,
                'description' => 'Kaos motif garis retro yang nyaman',
                'image' => 'products/baju/depanbaju8.webp',
            ],

            // Celana (Category 2)
            [
                'category_id' => 2,
                'name' => 'Streetwear Cargo Pants',
                'price' => 280000,
                'weight' => 800,
                'stock' => 10,
                'description' => 'Celana kargo streetwear premium',
                'image' => 'products/celana/depancelana1.webp',
            ],

            // Topi (Category 3)
            [
                'category_id' => 3,
                'name' => 'Topi STS Classic',
                'price' => 158000,
                'weight' => 200,
                'stock' => 10,
                'description' => 'Topi nyaman dan stylish',
                'image' => 'products/topi/topi1.webp',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✓ ' . count($products) . ' products created successfully.');
    }
}
