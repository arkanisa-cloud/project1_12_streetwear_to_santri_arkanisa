<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 * Controller untuk halaman dashboard admin
 * Menampilkan statistik dan ringkasan data
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin
     */
    public function index()
    {
        // Total data
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        // Data stok
        $lowStock = Product::where('stock', '<=', 5)->count();
        $outOfStock = Product::where('stock', 0)->count();
        $stockInToday = StockIn::whereDate('created_at', today())->sum('qty');
        $stockOutToday = StockOut::whereDate('created_at', today())->sum('qty');

        // Penjualan hari ini (berdasarkan zona waktu Asia/Jakarta untuk WIB)
        $timezone = 'Asia/Jakarta';
        $todayStart = now($timezone)->startOfDay()->utc();
        $todayEnd = now($timezone)->endOfDay()->utc();

        $salesToday = Order::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('payment_status', 'paid')
            ->sum('total');

        // Produk stok menipis (<= 5)
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->with('category')
            ->get();

        // Order terbaru
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Grafik penjualan 7 hari terakhir dengan timezone & auto-filling data kosong
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now($timezone)->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $chartData[$dateStr] = [
                'date' => $dateStr,
                'label' => $date->translatedFormat('d M'), // e.g. "02 Jun"
                'day' => $date->translatedFormat('l'), // e.g. "Selasa"
                'total_orders' => 0,
                'total_sales' => 0,
            ];
        }

        $startDate = now($timezone)->subDays(6)->startOfDay()->utc();
        $endDate = now($timezone)->endOfDay()->utc();

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->get();

        foreach ($orders as $order) {
            $orderLocalDate = $order->created_at->timezone($timezone)->format('Y-m-d');
            if (isset($chartData[$orderLocalDate])) {
                $chartData[$orderLocalDate]['total_orders'] += 1;
                $chartData[$orderLocalDate]['total_sales'] += (float)$order->total;
            }
        }

        $salesChart = collect(array_values($chartData));

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCustomers',
            'pendingOrders',
            'completedOrders',
            'lowStock',
            'outOfStock',
            'stockInToday',
            'stockOutToday',
            'salesToday',
            'lowStockProducts',
            'recentOrders',
            'salesChart'
        ));
    }
}
