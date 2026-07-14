<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Category
 * Merepresentasikan kategori produk
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Relasi: Kategori punya banyak produk
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope: Filter kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Accessor: Status dalam bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            default => 'Unknown',
        };
    }
}
