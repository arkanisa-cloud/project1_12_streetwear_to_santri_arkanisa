<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentCallbackController extends Controller
{
    /**
     * Handle Notification / Callback webhook dari Midtrans
     */
    public function handleNotification(Request $request)
    {
        $payload = $request->all();
        
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;

        // 1. Keamanan: Validasi signature key bawaan Midtrans[cite: 7]
        $signatureKey = hash("sha512", $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));
        if ($signatureKey !== ($payload['signature_key'] ?? '')) {
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        // 2. Cari order berdasarkan order_number dari payload midtrans[cite: 7]
        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Jalankan dalam Database Transaction agar update ke tabel order & payment aman
        DB::beginTransaction();
        try {
            // Ambil data payment record terkait (opsional untuk sinkronisasi histori payment)
            $payment = Payment::where('order_id', $order->id)->first();

            // 3. Logic Perubahan Status Otomatis yang sinkron dengan Enum Database[cite: 8]
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                
                // Pembayaran Berhasil: update payment_status, bukan status utama![cite: 8]
                $order->update([
                    'payment_status' => 'paid',
                    // status utama tetap 'pending' atau otomatis berubah jadi 'processed' siap kirim[cite: 8]
                    'status' => 'processed' 
                ]);

                if ($payment) {
                    $payment->update(['status' => 'success']);
                }

            } elseif ($transactionStatus == 'pending') {
                
                $order->update([
                    'payment_status' => 'pending' // Menunggu user menyelesaikan transfer di ATM/App[cite: 8]
                ]);

                if ($payment) {
                    $payment->update(['status' => 'pending']);
                }

            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                
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