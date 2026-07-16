<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel payments untuk menyimpan data pembayaran
     * Setiap order punya satu payment (one-to-one)
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Relasi ke orders
<<<<<<< HEAD
            $table->enum('payment_method', ['transfer', 'ewallet', 'cod'])->default('transfer'); // Metode pembayaran
=======
            $table->enum('payment_method', ['midtrans', 'cod'])->default('midtrans'); // Metode pembayaran
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
            $table->string('proof')->nullable(); // File bukti pembayaran
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending'); // Status verifikasi
            $table->text('admin_notes')->nullable(); // Catatan admin (misal: alasan penolakan)
            $table->timestamp('verified_at')->nullable(); // Waktu verifikasi
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel payments
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
