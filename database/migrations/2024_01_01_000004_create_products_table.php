<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel products untuk menyimpan data produk
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Relasi ke categories
            $table->string('name'); // Nama produk
            $table->decimal('price', 10, 2); // Harga produk (maksimal 99.999.999,99)
            $table->unsignedInteger('weight');
            $table->integer('stock')->default(0); // Jumlah stok tersedia
            $table->text('description')->nullable(); // Deskripsi produk
            $table->string('image')->nullable(); // Path/nama file gambar
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel products
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
