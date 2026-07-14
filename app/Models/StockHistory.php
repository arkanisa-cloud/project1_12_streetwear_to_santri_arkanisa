<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model StockHistory
 * Merepresentasikan riwayat perubahan stok
 * Setiap perubahan stok dicatat untuk audit trail
 */
class StockHistory extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'product_id',
        'type',
        'qty',
        'stock_before',
        'stock_after',
        'reference_id',
        'reference_type',
    ];

    /**
     * Relasi: Riwayat belongs to produk
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi: Polymorphic ke referensi (StockIn, StockOut, atau Order)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Accessor: Type dalam bahasa Indonesia
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'in' => 'Stok Masuk',
            'out' => 'Stok Keluar',
            'sale' => 'Penjualan',
            default => 'Unknown',
        };
    }

    /**
     * Accessor: Format perubahan stok
     */
    public function getChangeTextAttribute(): string
    {
        $sign = $this->type === 'in' ? '+' : '-';
        return "{$sign}{$this->qty}";
    }

    /**
     * Scope: Filter riwayat stok masuk
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope: Filter riwayat stok keluar
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }

    /**
     * Scope: Filter riwayat penjualan
     */
    public function scopeSale($query)
    {
        return $query->where('type', 'sale');
    }
}
