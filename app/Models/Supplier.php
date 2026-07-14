<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Supplier
 * Merepresentasikan supplier/pemasok barang
 */
class Supplier extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    /**
     * Relasi: Supplier punya banyak stok masuk
     */
    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }
}
