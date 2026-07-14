<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', 
            'back_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $this->convertToWebp($request->file('image'));
        }

        if ($request->hasFile('back_image')) {
            $validated['back_image'] = $this->convertToWebp($request->file('back_image'));
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', 
            'back_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Handle gambar depan (front POV)
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete($product->image);
            }
            $validated['image'] = $this->convertToWebp($request->file('image'));
        }

        // Handle gambar belakang (back POV)
        if ($request->hasFile('back_image')) {
            if ($product->back_image) {
                Storage::delete($product->back_image);
            }
            $validated['back_image'] = $this->convertToWebp($request->file('back_image'));
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        // Hapus kedua gambar dari storage
        if ($product->image) {
            Storage::delete($product->image);
        }
        if ($product->back_image) {
            Storage::delete($product->back_image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Fungsi Helper Native PHP untuk Konversi ke WebP
     */
    private function convertToWebp($file)
    {
        // 0. Tingkatkan batas memori sementara jadi 512MB khusus untuk proses ini
        ini_set('memory_limit', '512M');

        $sourcePath = $file->getPathname();
        $mime = $file->getMimeType();
        $sourceImage = null;

        // 1. Baca gambar ke memori berdasarkan format aslinya
        if ($mime == 'image/jpeg') {
            $sourceImage = imagecreatefromjpeg($sourcePath);
        } elseif ($mime == 'image/png') {
            $sourceImage = imagecreatefrompng($sourcePath);
            
            // Pertahankan transparansi (alpha channel) agar background PNG tidak jadi hitam
            imagepalettetotruecolor($sourceImage);
            imagealphablending($sourceImage, true);
            imagesavealpha($sourceImage, true);
        }

        if ($sourceImage) {
            // 2. Gunakan Output Buffering (ob_start) untuk menangkap hasil konversi dari memori
            ob_start();
            imagewebp($sourceImage, null, 80); // Angka 80 adalah kualitas kompresi (0-100)
            $imageContent = ob_get_clean();
            
            // 3. Bersihkan RAM
            imagedestroy($sourceImage);

            // 4. Simpan menggunakan Storage Laravel agar konsisten dengan ekosistem aplikasi
            $filename = 'products/' . uniqid('sts_') . '.webp';
            Storage::put($filename, $imageContent);

            return $filename;
        }

        // Fallback: Jika PHP gagal mengonversi (misal karena file rusak), jalankan metode bawaan Laravel
        return $file->store('products');
    }
}