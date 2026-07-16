<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Order
 * Merepresentasikan pesanan customer
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara mass assignment
     */
    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'order_number',
        'total',
        'status',
        'payment_status',
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
<<<<<<< HEAD
=======
        'snap_token',
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
    ];

    /**
     * Atribut yang harus di-cast ke tipe tertentu
     */
    protected $casts = [
        'total' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    /**
     * Relasi: Order belongs to user (customer)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Order punya banyak order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi: Order punya satu payment
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relasi: Order uses shipping address
     */
    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    /**
     * Accessor: Format total ke Rupiah
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    /**
     * Accessor: Status order dalam bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Diproses',
            'processed' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    /**
     * Accessor: Status pembayaran dalam bahasa Indonesia
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'Belum Bayar',
            'pending' => 'Menunggu Verifikasi',
            'paid' => 'Sudah Bayar',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    /**
     * Cek apakah order bisa dibayar
     */
    public function canPay(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    /**
     * Cek apakah order bisa diproses
     */
    public function canProcess(): bool
    {
        return $this->payment_status === 'paid' && $this->status === 'pending';
    }

    /**
     * Cek apakah order sudah dibayar
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Generate nomor order unik
     * Format: ORD-YYYYMMDD-XXXX (contoh: ORD-20240104-0001)
     */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');

        // Cari order terakhir hari ini
        $lastOrder = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        // Generate sequence number
        $sequence = $lastOrder
            ? (int)substr($lastOrder->order_number, -4) + 1
            : 1;

        // Pad dengan 0 di depan (0001, 0002, dst)
        return 'ORD-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope: Filter order yang pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Filter order yang sudah dibayar
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
