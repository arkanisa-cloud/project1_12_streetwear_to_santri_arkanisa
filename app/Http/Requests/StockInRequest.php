<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StockInRequest
 * Validasi untuk form stok masuk
 */
class StockInRequest extends FormRequest
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
        return [
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'tanggal_masuk' => 'required|date|before_or_equal:today',
            'qty' => 'required|integer|min:1|max:999999',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih',
            'product_id.exists' => 'Produk tidak valid',
            'supplier_id.exists' => 'Supplier tidak valid',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi',
            'tanggal_masuk.before_or_equal' => 'Tanggal tidak boleh masa depan',
            'qty.required' => 'Jumlah wajib diisi',
            'qty.integer' => 'Jumlah harus bilangan bulat',
            'qty.min' => 'Jumlah minimal 1',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter',
        ];
    }
}
