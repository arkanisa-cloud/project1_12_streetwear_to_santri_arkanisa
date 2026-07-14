<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CheckoutRequest
 * Validasi untuk form checkout
 */
class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isCustomer();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
            'payment_method' => 'required|in:transfer,ewallet,cod',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'shipping_address_id.required' => 'Alamat pengiriman wajib dipilih',
            'shipping_address_id.exists' => 'Alamat tidak valid',
            'payment_method.required' => 'Metode pembayaran wajib dipilih',
            'payment_method.in' => 'Metode pembayaran tidak valid',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $addressId = $this->input('shipping_address_id');

            if ($addressId) {
                $address = \App\Models\ShippingAddress::find($addressId);

                if (!$address || $address->user_id !== auth()->id()) {
                    $validator->errors()->add('shipping_address_id', 'Alamat tidak valid atau bukan milik Anda');
                }
            }
        });
    }
}
