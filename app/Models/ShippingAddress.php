<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ShippingAddress
 * Merepresentasikan alamat pengiriman customer
 */
class ShippingAddress extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone',
        'address',
        'province_id',
        'city_id',
        'city',
        'subdistrict_id',
        'subdistrict',
        'province',
        'postal_code',
    ];

    /**
     * Relasi: Alamat belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Alamat punya banyak order
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Accessor: Alamat lengkap dalam satu baris
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->subdistrict}, {$this->city}, {$this->province} {$this->postal_code}";
    }
}
