<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel categories untuk menyimpan data kategori produk
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama kategori, contoh: Elektronik, Pakaian
            $table->text('description')->nullable(); // Deskripsi kategori
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel categories
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
