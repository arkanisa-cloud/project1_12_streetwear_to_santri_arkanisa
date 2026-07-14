<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model CartItem
 * Merepresentasikan item dalam keranjang belanja
 */
class CartItem extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'qty',
        'is_selected',
        'price',
    ];

    /**
     * Atribut yang harus di-cast ke tipe tertentu
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_selected' => 'boolean',
    ];

    /**
     * Relasi: Cart item belongs to cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relasi: Cart item belongs to product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessor: Hitung subtotal (qty × price)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->qty * $this->price;
    }

    /**
     * Accessor: Format subtotal ke Rupiah
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Method: Update qty dengan validasi stok
     */
    public function updateQty(int $qty): bool
    {
        // Validasi stok
        if (!$this->product->hasStock($qty)) {
            throw new \Exception("Stok tidak mencukupi!");
        }

        return $this->update(['qty' => $qty]);
    }
}
