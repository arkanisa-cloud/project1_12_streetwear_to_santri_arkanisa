<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockIn;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * StockInController
 * Controller untuk mengelola stok masuk
 */
class StockInController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan semua stok masuk
     */
    public function index()
    {
        $stockIns = StockIn::with(['product', 'supplier'])
            ->latest()
            ->get();

        return view('admin.stock-ins.index', compact('stockIns'));
    }

    /**
     * Show the form for creating a new resource.
     * Tampilkan form input stok masuk
     */
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        return view('admin.stock-ins.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     * Simpan stok masuk dengan transaction
     *
     * LOGIC:
     * 1. Stok baru = Stok lama + Qty masuk
     * 2. Catat di stock_ins
     * 3. Update stok di products
     * 4. Catat di stock_histories
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'tanggal_masuk' => 'required|date|before_or_equal:today',
            'qty' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        // Gunakan database transaction untuk konsistensi data
        DB::beginTransaction();
        try {
            // Ambil data produk
            $product = Product::findOrFail($validated['product_id']);

            // Simpan stok masuk
            $stockIn = StockIn::create($validated);

            // Hitung stok baru
            // LOGIC: stok_baru = stok_lama + qty_masuk
            $stockBefore = $product->stock;
            $stockAfter = $stockBefore + $validated['qty'];

            // Update stok produk
            $product->update(['stock' => $stockAfter]);

            // Catat di stock history
            StockHistory::create([
                'product_id' => $product->id,
                'type' => 'in',
                'qty' => $validated['qty'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_id' => $stockIn->id,
                'reference_type' => 'StockIn',
            ]);

            // Commit transaction
            DB::commit();

            return redirect()
                ->route('admin.stock-ins.index')
                ->with('success', 'Stok masuk berhasil dicatat. ' .
                    "Stok {$product->name}: {$stockBefore} → {$stockAfter}");

        } catch (\Exception $e) {
            // Rollback jika ada error
            DB::rollback();
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * Hapus data stok masuk (opsional, hati-hati!)
     * WARNING: Tidak diimplementasikan untuk safety
     * Rollback stok harus dilakukan manual
     */
    public function destroy(StockIn $stockIn)
    {
        // Untuk safety, kita tidak menghapus stok masuk
        // Rollback harus dilakukan dengan membuat stok keluar
        return back()->with('error', 'Tidak bisa menghapus data stok masuk. ' .
            'Jika salah input, silakan buat stok keluar untuk memperbaikinya.');
    }
}
