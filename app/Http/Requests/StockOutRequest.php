<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StockOutRequest
 * Validasi untuk form stok keluar
 */
class StockOutRequest extends FormRequest
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
            'tanggal_keluar' => 'required|date|before_or_equal:today',
            'qty' => 'required|integer|min:1|max:999999',
            'alasan' => 'required|in:rusak,hilang,kadaluarsa,lainnya',
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
            'tanggal_keluar.required' => 'Tanggal keluar wajib diisi',
            'tanggal_keluar.before_or_equal' => 'Tanggal tidak boleh masa depan',
            'qty.required' => 'Jumlah wajib diisi',
            'qty.integer' => 'Jumlah harus bilangan bulat',
            'qty.min' => 'Jumlah minimal 1',
            'alasan.required' => 'Alasan wajib dipilih',
            'alasan.in' => 'Alasan tidak valid',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $productId = $this->input('product_id');
            $qty = $this->input('qty');

            if ($productId && $qty) {
                $product = \App\Models\Product::find($productId);

                if ($product && $qty > $product->stock) {
                    $validator->errors()->add('qty', "Stok tidak mencukupai! Stok tersedia: {$product->stock}, Diminta: {$qty}");
                }
            }
        });
    }
}
