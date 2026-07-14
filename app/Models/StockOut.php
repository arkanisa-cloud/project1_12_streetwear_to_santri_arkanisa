<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model StockOut
 * Merepresentasikan stok keluar (non-sale)
 * Barang keluar karena: rusak, hilang, kadaluarsa, dll
 */
class StockOut extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'product_id',
        'tanggal_keluar',
        'qty',
        'alasan',
        'keterangan',
    ];

    /**
     * Atribut yang harus di-cast ke tipe tertentu
     */
    protected $casts = [
        'tanggal_keluar' => 'date',
    ];

    /**
     * Relasi: Stok keluar belongs to produk
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi: Stok keluar punya satu history
     */
    public function stockHistory()
    {
        return $this->hasOne(StockHistory::class, 'reference_id')
            ->where('reference_type', 'StockOut');
    }

    /**
     * Accessor: Alasan dalam bahasa Indonesia
     */
    public function getAlasanLabelAttribute(): string
    {
        return match($this->alasan) {
            'rusak' => 'Barang Rusak',
            'hilang' => 'Barang Hilang',
            'kadaluarsa' => 'Kadaluarsa',
            'lainnya' => 'Lainnya',
            default => 'Unknown',
        };
    }
}
