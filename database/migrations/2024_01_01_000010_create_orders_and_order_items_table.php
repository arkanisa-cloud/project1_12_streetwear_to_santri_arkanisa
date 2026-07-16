<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel orders dan order_items
     * Orders: Data pesanan customer
     * Order Items: Detail item dalam pesanan
     */
    public function up(): void
    {
        // Tabel orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Customer yang order
            $table->foreignId('shipping_address_id')->nullable()->constrained()->onDelete('set null'); // Alamat pengiriman
            $table->string('order_number')->unique(); // Nomor order unik (contoh: ORD-20240104-0001)
            $table->decimal('total', 10, 2); // Total harga pesanan
            $table->enum('status', ['pending', 'processed', 'shipped', 'completed', 'cancelled'])->default('pending'); // Status order
            $table->string('shipping_courier')->nullable(); // Contoh: jne, pos, tiki
            $table->string('shipping_service')->nullable(); // Contoh: OKE, REG
            $table->decimal('shipping_cost', 10, 2)->nullable()->default(0); // Biaya pengirim
            // $table->string('snap_token')->nullable();
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'rejected'])->default('unpaid'); // Status pembayaran
            $table->timestamps();
        });

        // Tabel order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Relasi ke orders
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Produk yang dibeli
            $table->integer('qty'); // Jumlah barang
            $table->decimal('price', 10, 2); // Harga saat transaksi (disimpan untuk histori)
            $table->decimal('subtotal', 10, 2); // Subtotal = qty × price
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel order_items dan orders
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
