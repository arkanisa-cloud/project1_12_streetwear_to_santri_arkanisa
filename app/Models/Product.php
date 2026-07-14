<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Model Product
 * Merepresentasikan produk yang dijual
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'weight',
        'stock',
        'description',
        'image',
        'back_image',
    ];

    /**
     * Atribut yang harus di-cast ke tipe tertentu
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relasi: Produk belongs to kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier() {
    return $this->belongsTo(Supplier::class);
}

    /**
     * Relasi: Produk punya banyak riwayat stok
     */
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Relasi: Produk punya banyak pesanan
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Accessor: Format harga ke Rupiah
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Accessor: URL gambar produk
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-image.png'); // Gambar default
    }

    /**
     * Accessor: URL gambar belakang produk (jika ada)
     */
    public function getBackImageUrlAttribute(): ?string
    {
        if ($this->back_image) {
            return asset('storage/' . $this->back_image);
        }
        return null;
    }

    /**
     * Cek apakah stok tersedia cukup
     */
    public function hasStock(int $qty): bool
    {
        return $this->stock >= $qty;
    }

    /**
     * Cek apakah produk tersedia (stok > 0)
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Scope: Filter produk dengan stok menipis
     */
    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->where('stock', '<=', $threshold);
    }

    /**
     * Scope: Filter produk dengan stok habis
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', 0);
    }

    /**
     * Scope: Filter produk tersedia
     */
    public function scopeAvailable($query)
    {
        return $query;
    }
}
