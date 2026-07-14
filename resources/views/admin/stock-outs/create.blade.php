@extends('layouts.admin')

@section('title', 'Input Stok Keluar')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-zinc-950 uppercase italic">Record Stock Out</h1>
                <p class="text-sm text-zinc-500">Record inventory reductions and stock movements.</p>
            </div>
            <a href="{{ route('admin.stock-outs.index') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-bold uppercase tracking-widest transition-all rounded-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Form Container -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <!-- Warning Card -->
                <div class="bg-red-50 border-b border-red-100 p-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-red-800 uppercase tracking-widest">Important Notice</h3>
                            <p class="text-sm text-red-700 mt-1">Stock out will reduce available inventory.<br>
                                <strong>Formula:</strong> New Stock = Current Stock - Outgoing Quantity
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.stock-outs.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Product Selection -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">
                            Product <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="product_id"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 @error('product_id') border-red-300 @enderror"
                            required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-stock="{{ $product->stock }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Available Stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date Input -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">
                            Out Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_keluar" id="tanggal_keluar"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 @error('tanggal_keluar') border-red-300 @enderror"
                            value="{{ old('tanggal_keluar', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}"
                            required>
                        @error('tanggal_keluar')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Quantity Input -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="qty" id="qty"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 @error('qty') border-red-300 @enderror"
                            value="{{ old('qty') }}" min="1" placeholder="Example: 3" required>
                        @error('qty')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                        <p class="text-xs text-zinc-400 mt-1">Cannot exceed available stock</p>
                    </div>

                    <!-- Reason Selection -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">
                            Reason <span class="text-red-500">*</span>
                        </label>
                        <select name="alasan" id="alasan"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 @error('alasan') border-red-300 @enderror"
                            required>
                            <option value="rusak" {{ old('alasan') === 'rusak' ? 'selected' : '' }}>Damaged Goods</option>
                            <option value="hilang" {{ old('alasan') === 'hilang' ? 'selected' : '' }}>Lost Items</option>
                            <option value="kadaluarsa" {{ old('alasan') === 'kadaluarsa' ? 'selected' : '' }}>Expired
                            </option>
                            <option value="lainnya" {{ old('alasan') === 'lainnya' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('alasan')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">
                            Notes
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 resize-none"
                            placeholder="Explain the reason in detail...">{{ old('keterangan') }}</textarea>
                    </div>

                    <!-- Stock Preview -->
                    <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-4" id="stockPreview">
                        <div class="flex items-center space-x-2 mb-2">
                            <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <span class="text-sm font-bold text-zinc-700 uppercase tracking-widest">Preview</span>
                        </div>
                        <p class="text-sm text-zinc-600" id="previewText">Select product and quantity to see preview</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-widest transition-all rounded-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Save Stock Out
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview stock with validation
        document.getElementById('product_id').addEventListener('change', updatePreview);
        document.getElementById('qty').addEventListener('input', updatePreview);

        function updatePreview() {
            const productSelect = document.getElementById('product_id');
            const qtyInput = document.getElementById('qty');
            const previewText = document.getElementById('previewText');
            const previewDiv = document.getElementById('stockPreview');

            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const stockBefore = selectedOption.dataset.stock || 0;
            const qty = parseInt(qtyInput.value) || 0;

            if (productSelect.value && qty > 0) {
                const stockAfter = parseInt(stockBefore) - qty;

                if (stockAfter < 0) {
                    // Invalid - negative stock
                    previewDiv.className = 'bg-red-50 border border-red-200 rounded-lg p-4';
                    previewText.innerHTML = `
                        <svg class="w-4 h-4 text-red-600 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <strong class="text-red-800">ERROR:</strong> Insufficient stock!<br>
                        <span class="font-medium">Available:</span> <strong class="text-zinc-900">${stockBefore}</strong>,
                        <span class="font-medium">Requested:</span> <strong class="text-red-600">${qty}</strong>
                    `;
                } else {
                    // Valid
                    previewDiv.className = 'bg-zinc-50 border border-zinc-200 rounded-lg p-4';
                    previewText.innerHTML = `
                        <span class="font-medium">Current Stock:</span> <strong class="text-zinc-900">${stockBefore}</strong> -
                        <span class="font-medium">Outgoing Qty:</span> <strong class="text-red-600">${qty}</strong> =
                        <span class="font-medium">New Stock:</span> <strong class="text-red-700">${stockAfter}</strong>
                    `;
                }
            } else {
                previewDiv.className = 'bg-zinc-50 border border-zinc-200 rounded-lg p-4';
                previewText.textContent = 'Select product and quantity to see preview';
            }
        }
    </script>
@endsection
