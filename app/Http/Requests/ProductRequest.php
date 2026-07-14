<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ProductRequest
 * Validasi untuk form produk
 */
class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'stock' => 'required|integer|min:0|max:999999',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih',
            'category_id.exists' => 'Kategori tidak valid',
            'name.required' => 'Nama produk wajib diisi',
            'price.required' => 'Harga wajib diisi',
            'price.numeric' => 'Harga harus angka',
            'price.min' => 'Harga tidak boleh negatif',
            'stock.required' => 'Stok wajib diisi',
            'stock.integer' => 'Stok harus bilangan bulat',
            'stock.min' => 'Stok tidak boleh negatif',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus JPEG, PNG, atau JPG',
            'image.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}
