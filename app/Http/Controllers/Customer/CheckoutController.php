<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\StockHistory;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CheckoutController
 * Controller untuk proses checkout order
 *
 * LOGIC CHECKOUT:
 * 1. Validasi stok lagi (critical!)
 * 2. Buat order dengan nomor unik
 * 3. Buat order items
 * 4. Kurangi stok produk
 * 5. Catat di stock_histories
 * 6. Buat payment record
 * 7. Kosongkan cart
 * 8. Gunakan transaction untuk konsistensi
 */
class CheckoutController extends Controller
{

    /**
     * Show checkout page.
     * Tampilkan halaman checkout
     */
    public function index()
    {
        // Ambil cart user
        $cart = Cart::where('user_id', Auth::id())->first();

        // Validasi: Cart harus ada dan punya item yang dipilih
        if (!$cart || $cart->cartItems()->where('is_selected', true)->count() == 0) {
            return redirect()
                ->route('customer.cart.index')
                ->with('error', 'Silakan pilih barang yang ingin di-checkout!');
        }

        // Load cart items yang terpilih dengan produk
        $cart->load(['cartItems' => function($query) {
            $query->where('is_selected', true)->with('product');
        }]);

        // Ambil alamat pengiriman user
        $addresses = ShippingAddress::where('user_id', Auth::id())->get();

        return view('customer.checkout', compact('cart', 'addresses'));
    }

    /**
     * Process checkout.
     * Proses checkout dengan database transaction
     *
     * CRITICAL: Semua operasi harus dalam satu transaction!
     * Jika satu langkah gagal, semua harus dibatalkan (rollback)
     */
    public function store(Request $request)
    {
        // Validasi input - TAMBAHAN: shipping_courier, shipping_service, shipping_cost
        $validated = $request->validate([
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
            'payment_method' => 'required|in:transfer,ewallet,cod',
            'shipping_courier' => 'required|string|in:jne,jnt,pos,tiki,lion',
            'shipping_service' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        // Ambil cart user dengan item yang terpilih saja
        $cart = Cart::where('user_id', Auth::id())
            ->with(['cartItems' => function($query) {
                $query->where('is_selected', true)->with('product');
            }])
            ->first();

        // Validasi: Cart harus ada dan tidak kosong (yang terpilih)
        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()
                ->route('customer.cart.index')
                ->with('error', 'Tidak ada barang terpilih untuk di-checkout!');
        }

        // Validasi: Alamat harus milik user yang login
        $address = ShippingAddress::find($validated['shipping_address_id']);
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Hitung total keseluruhan (Hanya yang terpilih + Ongkir)
        $grandTotal = $cart->total + $validated['shipping_cost'];

        // ========================================
        // DATABASE TRANSACTION - CRITICAL SECTION
        // ========================================
        DB::beginTransaction();
        try {
            // ----------------------------------------
            // STEP 1: Validasi stok lagi (critical!)
            // ----------------------------------------
            // Stok mungkin sudah berubah sejak user add to cart
            foreach ($cart->cartItems as $item) {
                if (!$item->product->hasStock($item->qty)) {
                    throw new \Exception(
                        "Stok {$item->product->name} tidak mencukupan! " .
                        "Stok tersedia: {$item->product->stock}, " .
                        "diminta: {$item->qty}"
                    );
                }
            }

            // ----------------------------------------
            // STEP 2: Buat order
            // ----------------------------------------
            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address_id' => $validated['shipping_address_id'],
                'order_number' => Order::generateOrderNumber(),
                'total' => $grandTotal,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_courier' => $validated['shipping_courier'],
                'shipping_service' => $validated['shipping_service'],
                'shipping_cost' => $validated['shipping_cost'],
            ]);

            // ----------------------------------------
            // STEP 3: Buat order items & kurangi stok
            // ----------------------------------------
            foreach ($cart->cartItems as $item) {
                // Buat order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'subtotal' => $item->qty * $item->price,
                ]);

                // Ambil produk
                $product = $item->product;

                // Hitung stok baru
                $stockBefore = $product->stock;
                $stockAfter = $stockBefore - $item->qty;

                // Validasi stok tidak boleh minus
                if ($stockAfter < 0) {
                    throw new \Exception("Stok {$product->name} tidak mencukupan!");
                }

                // Update stok produk
                $product->update(['stock' => $stockAfter]);

                // Catat di stock history
                StockHistory::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'qty' => $item->qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference_id' => $order->id,
                    'reference_type' => 'Order',
                ]);
            }

            // ----------------------------------------
            // STEP 4: Buat payment record
            // ----------------------------------------
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
            ]);

            // ----------------------------------------
            // STEP 5: Hapus hanya item yang di-checkout dari cart
            // ----------------------------------------
            $cart->cartItems()->where('is_selected', true)->delete();

            // ========================================
            // COMMIT TRANSACTION
            // ========================================
            DB::commit();

            // Redirect ke halaman detail order
            return redirect()
                ->route('customer.orders.show', $order)
                ->with('success', 'Order berhasil dibuat! Silakan upload bukti pembayaran.');

        } catch (\Exception $e) {
            // ========================================
            // ROLLBACK TRANSACTION jika ada error
            // ========================================
            DB::rollback();

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Upload payment proof.
     * Upload bukti pembayaran
     */
    public function uploadPayment(Request $request, Order $order)
    {
        // Cek ownership
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Validasi input
        $validated = $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
        ]);

        // Upload bukti pembayaran
        $imagePath = $request->file('proof')->store('payments');

        // Update payment
        $order->payment->update([
            'proof' => $imagePath,
            'status' => 'pending', // Menunggu verifikasi admin
        ]);

        // Update order status
        $order->update(['payment_status' => 'pending']);

        return back()
            ->with('success', 'Bukti pembayaran berhasil diupload! ' .
                'Menunggu verifikasi admin.');
    }
}