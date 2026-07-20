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
use Midtrans\Config;
use Midtrans\Snap;

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
        // Validasi: pastikan payment_method menerima 'midtrans' dan 'cod'
        $validated = $request->validate([
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
            'payment_method' => 'required|in:midtrans,cod', 
            'shipping_courier' => 'required|string|in:jne,jnt,pos,tiki,lion',
            'shipping_service' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $cart = Cart::where('user_id', Auth::id())
            ->with(['cartItems' => function($query) {
                $query->where('is_selected', true)->with('product');
            }])
            ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return redirect()
                ->route('customer.cart.index')
                ->with('error', 'Tidak ada barang terpilih untuk di-checkout!');
        }

        $address = ShippingAddress::find($validated['shipping_address_id']);
        if ($address->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $grandTotal = $cart->total + $validated['shipping_cost'];

        DB::beginTransaction();
        try {
            // STEP 1: Validasi stok
            foreach ($cart->cartItems as $item) {
                if (!$item->product->hasStock($item->qty)) {
                    throw new \Exception(
                        "Stok {$item->product->name} tidak mencukupi! " .
                        "Stok tersedia: {$item->product->stock}, " .
                        "diminta: {$item->qty}"
                    );
                }
            }

            // STEP 2: Buat order dengan status awal 'pending' dan 'unpaid'
            $orderNumber = Order::generateOrderNumber();
            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address_id' => $validated['shipping_address_id'],
                'order_number' => $orderNumber,
                'total' => $grandTotal,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_courier' => $validated['shipping_courier'],
                'shipping_service' => $validated['shipping_service'],
                'shipping_cost' => $validated['shipping_cost'],
            ]);

            // STEP 3: Buat order items & kurangi stok
            foreach ($cart->cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'subtotal' => $item->qty * $item->price,
                ]);

                $product = $item->product;
                $stockBefore = $product->stock;
                $stockAfter = $stockBefore - $item->qty;

                if ($stockAfter < 0) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi!");
                }

                $product->update(['stock' => $stockAfter]);

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

            // STEP 4: Buat payment record awal
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
            ]);

            // STEP 5: Integrasi Jalur Pembayaran (Midtrans vs COD)
            if ($validated['payment_method'] === 'midtrans') {
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = config('midtrans.is_sanitized');
                Config::$is3ds = config('midtrans.is_3ds');

                $itemDetails = [];
                foreach ($cart->cartItems as $item) {
                    $itemDetails[] = [
                        'id' => 'prod-' . $item->product_id,
                        'name' => substr($item->product->name, 0, 50),
                        'price' => (int) $item->price,
                        'quantity' => (int) $item->qty,
                    ];
                }

                // Tambahkan ongkos kirim ke item_details agar total harga cocok dengan gross_amount
                if ($validated['shipping_cost'] > 0) {
                    $itemDetails[] = [
                        'id' => 'shipping-' . $validated['shipping_courier'],
                        'name' => 'Ongkos Kirim (' . strtoupper($validated['shipping_courier']) . ')',
                        'price' => (int) $validated['shipping_cost'],
                        'quantity' => 1,
                    ];
                }

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_number,
                        'gross_amount' => (int) $order->total,
                    ],
                    'item_details' => $itemDetails,
                    'customer_details' => [
                        'first_name' => Auth::user()->username,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->phone,
                    ],
                    'callbacks' => [
                        'finish' => route('customer.orders.show', $order->id),
                        'unfinish' => route('customer.orders.show', $order->id),
                        'error' => route('customer.orders.show', $order->id),
                    ]
                ];

                $snapToken = Snap::getSnapToken($params);
                
                // Pastikan migration table orders kamu punya field 'snap_token'
                $order->update(['snap_token' => $snapToken]); 

                // Hapus item terpilih dari cart sebelum commit
                $cart->cartItems()->where('is_selected', true)->delete();

                DB::commit();

                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('snapToken', $snapToken);
                    
            } elseif ($validated['payment_method'] === 'cod') {
                // Hapus item terpilih dari cart sebelum commit untuk COD
                $cart->cartItems()->where('is_selected', true)->delete();

                // Jalur COD: Tidak sentuh Midtrans, langsung commit perubahan
                DB::commit();

                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('success', 'Order berhasil dibuat dengan metode COD! Silakan siapkan uang tunai saat kurir datang.');
            }

        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}