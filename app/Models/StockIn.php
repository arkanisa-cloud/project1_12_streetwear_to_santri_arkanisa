<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model StockIn
 * Merepresentasikan stok masuk (barang masuk dari supplier)
 */
class StockIn extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'product_id',
        'supplier_id',
        'tanggal_masuk',
        'qty',
        'keterangan',
    ];

    /**
     * Atribut yang harus di-cast ke tipe tertentu
     */
    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    /**
     * Relasi: Stok masuk belongs to produk
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi: Stok masuk belongs to supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi: Stok masuk punya satu history
     */
    public function stockHistory()
    {
        return $this->hasOne(StockHistory::class, 'reference_id')
            ->where('reference_type', 'StockIn');
    }
}
