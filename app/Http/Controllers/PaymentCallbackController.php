<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Log;
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)

class PaymentCallbackController extends Controller
{
    /**
     * Handle Notification / Callback webhook dari Midtrans
     */
    public function handleNotification(Request $request)
    {
        $payload = $request->all();
<<<<<<< HEAD
        
=======
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;

<<<<<<< HEAD
        // 1. Keamanan: Validasi signature key bawaan Midtrans[cite: 7]
        $signatureKey = hash("sha512", $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));
        if ($signatureKey !== ($payload['signature_key'] ?? '')) {
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        // 2. Cari order berdasarkan order_number dari payload midtrans[cite: 7]
        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
=======
        // Log incoming payload for debugging
        Log::info('Midtrans Webhook Received Payload:', $payload);

        // 1. Keamanan: Validasi signature key bawaan Midtrans
        $serverKey = config('midtrans.server_key');
        $signatureKey = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
        
        Log::info('Midtrans Signature Verification:', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'calculated_signature' => $signatureKey,
            'received_signature' => $payload['signature_key'] ?? null,
            'is_match' => ($signatureKey === ($payload['signature_key'] ?? ''))
        ]);

        if ($signatureKey !== ($payload['signature_key'] ?? '')) {
            Log::warning('Midtrans Signature mismatch for Order: ' . $orderId);
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        // 2. Cari order berdasarkan order_number dari payload midtrans
        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            Log::warning('Midtrans Order not found in database: ' . $orderId);
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Jalankan dalam Database Transaction agar update ke tabel order & payment aman
        DB::beginTransaction();
        try {
            // Ambil data payment record terkait (opsional untuk sinkronisasi histori payment)
            $payment = Payment::where('order_id', $order->id)->first();

<<<<<<< HEAD
            // 3. Logic Perubahan Status Otomatis yang sinkron dengan Enum Database[cite: 8]
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                
                // Pembayaran Berhasil: update payment_status, bukan status utama![cite: 8]
                $order->update([
                    'payment_status' => 'paid',
                    // status utama tetap 'pending' atau otomatis berubah jadi 'processed' siap kirim[cite: 8]
=======
            // 3. Logic Perubahan Status Otomatis yang sinkron dengan Enum Database
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                
                // Pembayaran Berhasil: update payment_status & status order
                $order->update([
                    'payment_status' => 'paid',
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
                    'status' => 'processed' 
                ]);

                if ($payment) {
<<<<<<< HEAD
                    $payment->update(['status' => 'success']);
                }

=======
                    // Enum payments.status: 'pending', 'verified', 'rejected'
                    $payment->update([
                        'status' => 'verified',
                        'verified_at' => now(),
                    ]);
                }

                Log::info('Midtrans Payment SUCCESS - Order updated to paid/processed', [
                    'order_number' => $orderId,
                    'order_status' => $order->fresh()->status,
                    'payment_status' => $order->fresh()->payment_status,
                ]);

>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)
            } elseif ($transactionStatus == 'pending') {
                
                $order->update([
                    'payment_status' => 'pending' // Menunggu user menyelesaikan transfer di ATM/App[cite: 8]
                ]);

                if ($payment) {
                    $payment->update(['status' => 'pending']);
                }

            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
<<<<<<< HEAD
                
                // Pembayaran Gagal/Kedaluwarsa: batalkan orderan dan kembalikan stok[cite: 8]
                $order->update([
                    'payment_status' => 'rejected', //[cite: 8]
                    'status' => 'cancelled'          // Order dibatalkan otomatis[cite: 8]
                ]);

                if ($payment) {
                    $payment->update(['status' => 'failed']);
                }

                // SINKRONISASI STOK: Kembalikan stok produk karena orderan batal/expired
                $order->load('orderItems.product');
                foreach ($order->orderItems as $item) {
                    $product = $item->product;
                    $product->increment('stock', $item->qty); // Tambah kembali stoknya
                }
            }
=======
    
    // CEK DULU: Jika pesanan belum berstatus 'cancelled', baru kembalikan stok
    if ($order->status !== 'cancelled') {
        // SINKRONISASI STOK: Kembalikan stok produk karena orderan batal/expired
        $order->load('orderItems.product');
        foreach ($order->orderItems as $item) {
            $product = $item->product;
            $product->increment('stock', $item->qty);
        }

        // Baru update statusnya agar jika webhook masuk lagi, kondisi if ini akan dilewati
        $order->update([
            'payment_status' => 'rejected',
            'status' => 'cancelled'
        ]);
        
         if ($payment) {
            // Enum payments.status: 'pending', 'verified', 'rejected'
            $payment->update(['status' => 'rejected']);
        }
        
        Log::info('Midtrans Payment REJECTED/CANCELLED - Order updated', [
            'order_number' => $orderId,
            'status' => 'cancelled'
        ]);
    }
}
>>>>>>> 1cd85e2 (feat: Final Payment Gateway with Midtrans)

            DB::commit();
            return response()->json(['message' => 'Callback handled successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error processing callback',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}