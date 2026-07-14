<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Cart
 * Merepresentasikan keranjang belanja
 * Satu user punya satu cart
 */
class Cart extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Relasi: Cart belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Cart punya banyak cart items
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Accessor: Hitung total harga cart (hanya yang terpilih)
     * Total = Σ (qty × price) untuk semua items yang terpilih
     */
    public function getTotalAttribute(): float
    {
        return $this->cartItems->where('is_selected', true)->sum(function ($item) {
            return $item->qty * $item->price;
        });
    }

    /**
     * Accessor: Hitung total jumlah item (hanya yang terpilih)
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->cartItems->where('is_selected', true)->sum('qty');
    }

    /**
     * Accessor: Total semua item (tanpa filter selection)
     */
    public function getCountAllAttribute(): int
    {
        return $this->cartItems->count();
    }

    /**
     * Accessor: Hitung total berat cart (hanya yang terpilih)
     * Total weight = Σ (qty × weight) dalam Gram
     */
    public function getTotalWeightAttribute(): int
    {
        return $this->cartItems->where('is_selected', true)->sum(function ($item) {
            return $item->qty * ($item->product->weight ?? 500); // Default 500g if null
        });
    }

    /**
     * Accessor: Cek apakah cart kosong
     */
    public function getIsEmptyAttribute(): bool
    {
        return $this->cartItems->isEmpty();
    }

    /**
     * Method: Tambah item ke cart atau update qty jika sudah ada
     */
    public function addItem(Product $product, int $qty): CartItem
    {
        // Cek apakah produk sudah ada di cart
        $cartItem = $this->cartItems()
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update qty jika sudah ada
            $newQty = $cartItem->qty + $qty;

            // Validasi stok
            if (!$product->hasStock($newQty)) {
                throw new \Exception("Stok tidak mencukupi!");
            }

            $cartItem->update(['qty' => $newQty]);
        } else {
            // Tambah item baru
            // Validasi stok
            if (!$product->hasStock($qty)) {
                throw new \Exception("Stok tidak mencukupi!");
            }

            $cartItem = CartItem::create([
                'cart_id' => $this->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'price' => $product->price,
            ]);
        }

        return $cartItem->fresh();
    }

    /**
     * Method: Kosongkan cart
     */
    public function clear(): bool
    {
        return $this->cartItems()->delete();
    }
}
