<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Tambahkan dua import di bawah ini:
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * CartController
 * Controller untuk mengelola keranjang belanja
 */
class CartController extends Controller implements HasMiddleware
{
    /**
     * Pengganti Constructor untuk Middleware
     */
    public static function middleware(): array
    {
        return [
            // Ini akan menerapkan middleware 'auth' ke semua method
            new Middleware('auth'),
        ];
    }

    /**
     * Display cart.
     */
    public function index()
    {
        // Ambil atau buat cart user
        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['user_id' => Auth::id()]
        );

        // Load cart items dengan produk
        $cart->load('cartItems.product');

        return view('customer.cart', compact('cart'));
    }

    // ... method store, update, destroy, dan clear tetap sama ...
    
    /**
     * Store item to cart.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1|max:100',
        ]);

        // Ambil data produk
        $product = Product::findOrFail($validated['product_id']);

        // Cek stok
        if (!$product->hasStock($validated['qty'])) {
            return back()
                ->with('error', "Stok tidak mencukupan! Stok tersedia: {$product->stock}")
                ->withInput();
        }

        // Ambil atau buat cart user
        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['user_id' => Auth::id()]
        );

        // Cek apakah produk sudah ada di cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update qty jika sudah ada
            $newQty = $cartItem->qty + $validated['qty'];

            // Validasi stok lagi untuk total qty
            if (!$product->hasStock($newQty)) {
                return back()
                    ->with('error', "Total qty ({$newQty}) melebihi stok tersedia ({$product->stock})!")
                    ->withInput();
            }

            $cartItem->update(['qty' => $newQty]);
        } else {
            // Tambah item baru
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'qty' => $validated['qty'],
                'price' => $product->price,
            ]);
        }

        return back()
            ->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Jika request tidak mengirimkan qty, berarti ini adalah toggle pilihan (is_selected)
        if (!$request->has('qty')) {
             $cartItem->update([
                 'is_selected' => $request->boolean('is_selected')
             ]);
             return back();
        }

        $validated = $request->validate([
            'qty' => 'required|integer|min:1|max:100',
            'is_selected' => 'sometimes|boolean',
        ]);

        $product = $cartItem->product;

        if (isset($validated['qty']) && !$product->hasStock($validated['qty'])) {
            return back()
                ->with('error', "Stok tidak mencukupan! Stok tersedia: {$product->stock}");
        }

        $cartItem->update($validated);

        return back()
            ->with('success', 'Keranjang berhasil diperbarui!');
    }

    /**
     * Bulk action for cart items.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids) && in_array($action, ['delete'])) {
            return back()->with('error', 'Pilih barang terlebih dahulu!');
        }

        if ($action === 'delete') {
            CartItem::whereIn('id', $ids)
                ->whereHas('cart', function($q) {
                    $q->where('user_id', Auth::id());
                })->delete();
            return back()->with('success', 'Barang terpilih berhasil dihapus!');
        }

        if ($action === 'select_all') {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cart->cartItems()->update(['is_selected' => true]);
            }
            return back();
        }

        if ($action === 'deselect_all') {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cart->cartItems()->update(['is_selected' => false]);
            }
            return back();
        }

        return back();
    }

    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $cartItem->delete();

        return back()
            ->with('success', 'Item dihapus dari keranjang!');
    }

    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->cartItems()->delete();
        }

        return back()
            ->with('success', 'Keranjang dikosongkan!');
    }
}
