<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel stock_histories untuk audit trail perubahan stok
     * Setiap perubahan stok akan dicatat di sini
     */
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Relasi ke products
            $table->enum('type', ['in', 'out', 'sale']); // Jenis transaksi: masuk, keluar, terjual
            $table->integer('qty'); // Jumlah perubahan
            $table->integer('stock_before'); // Stok sebelum perubahan
            $table->integer('stock_after'); // Stok setelah perubahan
            $table->unsignedBigInteger('reference_id')->nullable(); // ID referensi (stock_in_id, stock_out_id, atau order_id)
            $table->string('reference_type')->nullable(); // Tipe referensi: StockIn, StockOut, atau Order
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel stock_histories
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
