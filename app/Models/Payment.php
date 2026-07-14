<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * Model Payment
 * Merepresentasikan pembayaran order
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'order_id',
        'payment_method',
        'proof',
        'status',
        'admin_notes',
        'verified_at',
    ];

    /**
     * Atribut yang harus di-cast ke tipe tertentu
     */
    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Relasi: Payment belongs to order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Accessor: Metode pembayaran dalam bahasa Indonesia
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'transfer' => 'Transfer Bank',
            'ewallet' => 'E-Wallet',
            'cod' => 'Cash on Delivery (COD)',
            default => 'Unknown',
        };
    }

    /**
     * Accessor: Status verifikasi dalam bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    /**
     * Accessor: URL bukti pembayaran
     */
    public function getProofUrlAttribute(): string
    {
        if ($this->proof) {
            return asset('storage/' . $this->proof);
        }
        return '';
    }

    /**
     * Cek apakah pembayaran sudah diverifikasi
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Cek apakah pembayaran ditolak
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Cek apakah menunggu verifikasi
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Method: Verifikasi pembayaran (approve)
     */
    public function verify(?string $notes = null): bool
    {
        return $this->update([
            'status' => 'verified',
            'admin_notes' => $notes,
            'verified_at' => now(),
        ]);
    }

    /**
     * Method: Tolak pembayaran (reject)
     */
    public function reject(string $reason): bool
    {
        return $this->update([
            'status' => 'rejected',
            'admin_notes' => $reason,
        ]);
    }
}
