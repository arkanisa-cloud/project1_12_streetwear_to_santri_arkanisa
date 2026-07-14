<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * CategoryController
 * Controller untuk CRUD kategori produk
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan semua kategori
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     * Tampilkan form tambah kategori
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     * Simpan kategori baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Simpan kategori
        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     * Tampilkan detail kategori
     */
    public function show(Category $category)
    {
        // Load produk dalam kategori
        $category->load('products');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     * Tampilkan form edit kategori
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * Update kategori
     */
    public function update(Request $request, Category $category)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Update kategori
        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     * Hapus kategori
     */
    public function destroy(Category $category)
    {
        // Cek apakah ada produk dalam kategori
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus kategori yang masih memiliki produk.');
        }

        // Hapus kategori
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
