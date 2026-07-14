<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Container\Attributes\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Membuat tabel shipping_addresses untuk menyimpan alamat pengiriman customer
     */
    public function up(): void
    {
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke users
            $table->string('recipient_name'); // Nama penerima paket
            $table->string('phone'); // Nomor HP penerima
            $table->text('address'); // Alamat lengkap jalan, RT/RW, nomor rumah
            
            // Komponen wilayah destinasi kurir RajaOngkir
            $table->unsignedInteger('city_id')->nullable(); // ID Kota dari RajaOngkir (Critical untuk API!)
            $table->string('city'); // Nama Kota/Kabupaten
            $table->string('province'); // Nama Provinsi
            $table->string('postal_code'); // Kode pos
            
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel shipping_addresses
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};