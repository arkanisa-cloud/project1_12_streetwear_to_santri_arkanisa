<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom role ke tabel users
     * Role: admin atau customer
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom role setelah kolom email
            $table->enum('role', ['admin', 'customer'])
                  ->default('customer')
                  ->after('email');
        });
    }

    /**
     * Rollback migration - hapus kolom role
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
