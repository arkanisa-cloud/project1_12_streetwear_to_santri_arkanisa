<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        Log::info('Midtrans Webhook Received Payload:', $payload);

        $serverKey = config('midtrans.server_key');
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        Log::info('Midtrans Signature Verification:', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'calculated_signature' => $signatureKey,
            'received_signature' => $payload['signature_key'] ?? null,
            'is_match' => ($signatureKey === ($payload['signature_key'] ?? '')),
        ]);

        if ($signatureKey !== ($payload['signature_key'] ?? '')) {
            Log::warning('Midtrans Signature mismatch for Order: ' . $orderId);
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            Log::warning('Midtrans Order not found in database: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::beginTransaction();
        try {
            $payment = Payment::where('order_id', $order->id)->first();

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processed',
                ]);

                if ($payment) {
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
            } elseif ($transactionStatus == 'pending') {
                $order->update([
                    'payment_status' => 'pending',
                ]);

                if ($payment) {
                    $payment->update(['status' => 'pending']);
                }
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                if ($order->status !== 'cancelled') {
                    $order->load('orderItems.product');
                    foreach ($order->orderItems as $item) {
                        $product = $item->product;
                        $product->increment('stock', $item->qty);
                    }

                    $order->update([
                        'payment_status' => 'rejected',
                        'status' => 'cancelled',
                    ]);

                    if ($payment) {
                        $payment->update(['status' => 'rejected']);
                    }

                    Log::info('Midtrans Payment REJECTED/CANCELLED - Order updated', [
                        'order_number' => $orderId,
                        'status' => 'cancelled',
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Callback handled successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error processing callback',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
