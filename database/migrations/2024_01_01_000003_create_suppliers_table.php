<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel suppliers untuk menyimpan data supplier/pemasok barang
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama supplier
            $table->string('phone')->nullable(); // Nomor telepon supplier
            $table->text('address')->nullable(); // Alamat supplier
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel suppliers
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
