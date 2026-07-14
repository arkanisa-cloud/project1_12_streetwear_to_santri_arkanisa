<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockOut;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * StockOutController
 * Controller untuk mengelola stok keluar (non-sale)
 * Barang keluar karena: rusak, hilang, kadaluarsa, dll
 */
class StockOutController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan semua stok keluar
     */
    public function index()
    {
        $stockOuts = StockOut::with('product')
            ->latest()
            ->get();

        return view('admin.stock-outs.index', compact('stockOuts'));
    }

    /**
     * Show the form for creating a new resource.
     * Tampilkan form input stok keluar
     */
    public function create()
    {
        $products = Product::all();
        return view('admin.stock-outs.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     * Simpan stok keluar dengan transaction
     *
     * LOGIC:
     * 1. Validasi: Stok tidak boleh minus
     * 2. Stok baru = Stok lama - Qty keluar
     * 3. Catat di stock_outs
     * 4. Update stok di products
     * 5. Catat di stock_histories
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'tanggal_keluar' => 'required|date|before_or_equal:today',
            'qty' => 'required|integer|min:1',
            'alasan' => 'required|in:rusak,hilang,kadaluarsa,lainnya',
            'keterangan' => 'nullable|string',
        ]);

        // Gunakan database transaction
        DB::beginTransaction();
        try {
            // Ambil data produk
            $product = Product::findOrFail($validated['product_id']);

            // VALIDASI KRUSIAL: Stok tidak boleh minus
            // Cek apakah stok cukup
            if ($product->stock < $validated['qty']) {
                throw new \Exception(
                    "Stok tidak mencukupi! Stok saat ini: {$product->stock}, " .
                    "diminta: {$validated['qty']}"
                );
            }

            // Simpan stok keluar
            $stockOut = StockOut::create($validated);

            // Hitung stok baru
            // LOGIC: stok_baru = stok_lama - qty_keluar
            $stockBefore = $product->stock;
            $stockAfter = $stockBefore - $validated['qty'];

            // Validasi tambahan (double check)
            if ($stockAfter < 0) {
                throw new \Exception("Stok tidak boleh minus!");
            }

            // Update stok produk
            $product->update(['stock' => $stockAfter]);

            // Catat di stock history
            StockHistory::create([
                'product_id' => $product->id,
                'type' => 'out',
                'qty' => $validated['qty'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_id' => $stockOut->id,
                'reference_type' => 'StockOut',
            ]);

            // Commit transaction
            DB::commit();

            return redirect()
                ->route('admin.stock-outs.index')
                ->with('success', 'Stok keluar berhasil dicatat. ' .
                    "Stok {$product->name}: {$stockBefore} → {$stockAfter}");

        } catch (\Exception $e) {
            // Rollback jika ada error
            DB::rollback();
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Tampilkan detail stok keluar
     */
    public function show(StockOut $stockOut)
    {
        $stockOut->load('product');
        return view('admin.stock-outs.show', compact('stockOut'));
    }

    /**
     * Remove the specified resource from storage.
     * Hapus data stok keluar (opsional, hati-hati!)
     * WARNING: Tidak diimplementasikan untuk safety
     */
    public function destroy(StockOut $stockOut)
    {
        // Untuk safety, kita tidak menghapus stok keluar
        // Jika salah input, silakan buat stok masuk untuk memperbaikinya
        return back()->with('error', 'Tidak bisa menghapus data stok keluar. ' .
            'Jika salah input, silakan buat stok masuk untuk memperbaikinya.');
    }
}
