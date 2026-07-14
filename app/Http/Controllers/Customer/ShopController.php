<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * ShopController
 * Controller untuk halaman shop customer
 * Menampilkan katalog produk
 */
class ShopController extends Controller
{
    /**
     * Display a listing of the products.
     * Menampilkan semua produk dengan filter & search
     */
    public function index(Request $request)
{
    // 1. Query dasar dengan Eager Loading
    $query = Product::with('category')->available();

    // 2. Filter berdasarkan kategori (Mendukung ID atau Slug)
    if ($request->has('category') && $request->category != '') {
        $categoryParam = $request->category;
        
        $query->whereHas('category', function($q) use ($categoryParam) {
            if (is_numeric($categoryParam)) {
                $q->where('id', $categoryParam);
            } else {
                $q->where('slug', $categoryParam);
            }
        });
    }

    // 3. Search berdasarkan nama produk
    if ($request->has('search') && $request->search != '') {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // TRICK UTAMA: Produk ready stock selalu di atas, produk stock habis (0) otomatis ke paling bawah
    $query->orderByRaw('stock > 0 DESC');

    // 4. Sorting Logic Tambahan (Berjalan setelah filter stok di atas)
    $sort = $request->get('sort', 'latest');
    switch ($sort) {
        case 'price_low':
            $query->orderBy('price', 'asc');
            break;
        case 'price_high':
            $query->orderBy('price', 'desc');
            break;
        case 'name':
            $query->orderBy('name', 'asc');
            break;
        case 'latest':
        default:
            $query->latest();
            break;
    }

    // 5. Eksekusi Pagination
    $products = $query->paginate(12)->withQueryString(); 

    // 6. Ambil data pendukung untuk View
    $categories = Category::active()->get();
    
    $selectedCategory = null;
    if ($request->filled('category')) {
        $selectedCategory = $categories->where('slug', $request->category)->first() 
                         ?? $categories->where('id', $request->category)->first();
    }

    return view('customer.shop', compact('products', 'categories', 'selectedCategory'));
}

    /**
     * Display the specified product.
     * Tampilkan detail produk
     */
    public function show(Product $product)
    {
        // Load relasi
        $product->load('category');

        // Produk terkait (kategori yang sama)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->available()
            ->take(4)
            ->get();

        return view('customer.product-detail', compact('product', 'relatedProducts'));
    }
}
