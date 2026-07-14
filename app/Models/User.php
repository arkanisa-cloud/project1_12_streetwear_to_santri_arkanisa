<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model User
 * Merepresentasikan data user (admin dan customer)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'role',
    ];

    /**
     * Kolom yang harus disembunyikan saat serialisasi
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast ke tipe tertentu
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi: User punya satu cart
     * Satu customer punya satu keranjang belanja
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Relasi: User punya banyak order
     * Satu customer bisa membuat banyak pesanan
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi: User punya banyak alamat pengiriman
     */
    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    /**
     * Cek apakah user ini admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user ini customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}
