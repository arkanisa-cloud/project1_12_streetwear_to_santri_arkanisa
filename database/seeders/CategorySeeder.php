<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * CategorySeeder
 * Seeder untuk membuat data kategori sample
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Baju',
                'description' => 'Baju berkualitas tinggi',
                'status' => 'active',
            ],

            [
                'name' => 'Celana',
                'description' => 'Celana trendy dan nyaman',
                'status' => 'active',
            ],

            [
                'name' => 'Topi',
                'description' => 'Topi keren untuk melengkapi gaya kamu',
                'status' => 'active',
            ],

            [
                'name' => 'Hoodie',
                'description' => 'Hoodie nyaman dan stylish',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✓ ' . count($categories) . ' categories created successfully.');
    }
}
