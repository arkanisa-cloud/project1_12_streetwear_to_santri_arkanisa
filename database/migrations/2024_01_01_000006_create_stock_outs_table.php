<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel stock_outs untuk pencatatan barang keluar (non-sale)
     * Barang keluar karena: rusak, hilang, kadaluarsa, dll
     */
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Relasi ke products
            $table->date('tanggal_keluar'); // Tanggal barang keluar
            $table->integer('qty'); // Jumlah barang keluar
            $table->enum('alasan', ['rusak', 'hilang', 'kadaluarsa', 'lainnya'])->default('lainnya'); // Alasan keluar
            $table->text('keterangan')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel stock_outs
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};
