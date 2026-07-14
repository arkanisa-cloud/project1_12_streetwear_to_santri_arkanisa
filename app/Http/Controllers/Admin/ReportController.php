<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * ReportController
 * Controller untuk laporan dan export
 */
class ReportController extends Controller
{
    /**
     * Laporan Stok
     */
    public function stock(Request $request)
    {
        $category_id = $request->get('category_id');
        $status = $request->get('status');

        $query = Product::with('category');

        // Filter by category
        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        // Filter by status
        if ($status === 'low') {
            $query->where('stock', '<=', 5);
        } elseif ($status === 'out') {
            $query->where('stock', 0);
        } elseif ($status === 'available') {
            $query->where('stock', '>', 0);
        }

        $products = $query->get();
        $categories = \App\Models\Category::all();

        return view('admin.reports.stock', compact('products', 'categories'));
    }

    /**
     * Laporan Penjualan
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $status = $request->get('status');

        $query = Order::with(['user', 'payment'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();

        // Hitung total penjualan
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total');
        $totalOrders = $orders->count();
        $paidOrders = $orders->where('payment_status', 'paid')->count();

        // Produk terlaris
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(qty) as total_sold'))
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.sales', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'paidOrders',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export laporan stok (contoh - bisa ditambahkan library export)
     */
    public function exportStock()
    {
        $products = Product::with('category')->get();

        // Untuk implementasi export, bisa pakai:
        // - Laravel Excel (https://github.com/Maatwebsite/Laravel-Excel)
        // - DomPDF untuk PDF

        return response()->json([
            'message' => 'Fitur export belum diimplementasikan',
            'data' => $products,
        ]);
    }

    /**
     * Export laporan penjualan (contoh)
     */
    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->get();

        return response()->json([
            'message' => 'Fitur export belum diimplementasikan',
            'data' => $orders,
        ]);
    }
}
