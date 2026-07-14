<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CategoryRequest
 * Validasi untuk form kategori
 */
class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya admin yang boleh mengakses
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi',
            'name.unique' => 'Nama kategori sudah ada',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ];
    }
}
