<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * OrderController (Admin)
 * Controller untuk admin mengelola pesanan
 */
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * List semua pesanan
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment']);

        // Filter berdasarkan status order
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search berdasarkan nomor order atau nama user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $orders = $query->latest()->get();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     * Detail pesanan
     */
    public function show(Order $order)
    {
        $order->load(['user', 'shippingAddress', 'orderItems.product', 'payment']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status.
     * Update status pesanan (pending → processed → shipped → completed)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:processed,shipped,completed,cancelled',
        ]);

        // Check if COD order
        $isCod = $order->payment && $order->payment->payment_method === 'cod';

        // Validasi: tidak bisa update jika belum bayar (kecuali cancel atau pesanan COD)
        if (!$isCod && $order->payment_status !== 'paid' && $validated['status'] !== 'cancelled') {
            return back()->with('error', 'Tidak bisa update status. Pesanan belum lunas.');
        }

        // Validasi flow status
        $statusFlow = [
            'pending' => ['processed', 'cancelled'],
            'processed' => ['shipped'],
            'shipped' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        $currentStatus = $order->status;
        $newStatus = $validated['status'];

        if (!in_array($newStatus, $statusFlow[$currentStatus] ?? [])) {
            return back()->with('error', 'Tidak bisa mengubah status dari ' . $currentStatus . ' ke ' . $newStatus);
        }

        // Jika COD selesai, maka otomatis lunas
        if ($isCod && $newStatus === 'completed') {
            $order->payment->update([
                'status' => 'verified',
                'verified_at' => now(),
                'admin_notes' => 'Pembayaran COD diterima saat pengiriman selesai',
            ]);
            $order->update([
                'status' => $newStatus,
                'payment_status' => 'paid'
            ]);
        } else {
            $order->update(['status' => $newStatus]);
        }

        return back()->with('success', 'Status pesanan berhasil diupdate menjadi ' . $newStatus);
    }

    /**
     * Verify payment.
     * Verifikasi pembayaran customer
     */
    public function verifyPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        if ($order->payment->status !== 'pending') {
            return back()->with('error', 'Pembayaran sudah diverifikasi sebelumnya.');
        }

        $payment = $order->payment;

        if ($validated['status'] === 'verified') {
            // Verifikasi berhasil
            $payment->verify($request->input('admin_notes'));

            // Update order payment status
            $order->update(['payment_status' => 'paid']);

            return back()->with('success', 'Pembayaran berhasil diverifikasi.');
        } else {
            // Tolak pembayaran
            $payment->reject($validated['admin_notes'] ?? 'Pembayaran tidak valid');

            // Update order payment status
            $order->update(['payment_status' => 'rejected']);

            return back()->with('success', 'Pembayaran ditolak.');
        }
    }
}
