<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model OrderItem
 * Merepresentasikan item dalam pesanan
 */
class OrderItem extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'price',
        'subtotal',
    ];

    /**
     * Atribut yang harus di-cast ke tipe tertentu
     */
    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi: Order item belongs to order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi: Order item belongs to product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor: Format subtotal ke Rupiah
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Accessor: Format price ke Rupiah
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
