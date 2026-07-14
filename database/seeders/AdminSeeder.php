<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder
 * Seeder untuk membuat user admin
 *
 * digunakan untuk membuat akun admin default
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user admin
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'admin',
        ]);

        $this->command->info('✓ Admin user created successfully.');
        $this->command->info('  Email: admin@example.com');
        $this->command->info('  Password: password');
    }
}
