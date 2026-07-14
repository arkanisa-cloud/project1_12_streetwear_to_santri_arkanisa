@extends('layouts.admin')

@section('title', 'Input Stok Masuk')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-2xl font-black tracking-tighter text-zinc-950 uppercase italic">Add Stock Entry</h1>
                <p class="text-sm text-zinc-500">Record new inventory additions to your stock.</p>
            </div>
            <a href="{{ route('admin.stock-ins.index') }}"
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
                <!-- Info Card -->
                <div class="bg-green-50 border-b border-green-100 p-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-green-800 uppercase tracking-widest">Stock Formula</h3>
                            <p class="text-sm text-green-700 mt-1">New Stock = Current Stock + Incoming Quantity</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.stock-ins.store') }}" method="POST" class="p-6 space-y-6">
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
                                    {{ $product->name }} (Current Stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Supplier Selection -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">
                            Supplier
                        </label>
                        <select name="supplier_id" id="supplier_id"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500">
                            <option value="">-- No Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-zinc-400 mt-1">Optional field</p>
                    </div>

                    <!-- Date Input -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-zinc-600 mb-2">
                            Entry Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_masuk" id="tanggal_masuk"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500 @error('tanggal_masuk') border-red-300 @enderror"
                            value="{{ old('tanggal_masuk', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}"
                            required>
                        @error('tanggal_masuk')
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
                            value="{{ old('qty') }}" min="1" placeholder="Example: 10" required>
                        @error('qty')
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
                            placeholder="Additional notes...">{{ old('keterangan') }}</textarea>
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
                            class="inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase tracking-widest transition-all rounded-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Save Stock Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview stock calculation
        document.getElementById('product_id').addEventListener('change', updatePreview);
        document.getElementById('qty').addEventListener('input', updatePreview);

        function updatePreview() {
            const productSelect = document.getElementById('product_id');
            const qtyInput = document.getElementById('qty');
            const previewText = document.getElementById('previewText');

            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const stockBefore = selectedOption.dataset.stock || 0;
            const qty = parseInt(qtyInput.value) || 0;

            if (productSelect.value && qty > 0) {
                const stockAfter = parseInt(stockBefore) + qty;
                previewText.innerHTML = `
                    <span class="font-medium">Current Stock:</span> <strong class="text-zinc-900">${stockBefore}</strong> +
                    <span class="font-medium">Incoming Qty:</span> <strong class="text-green-600">${qty}</strong> =
                    <span class="font-medium">New Stock:</span> <strong class="text-green-700">${stockAfter}</strong>
                `;
            } else {
                previewText.textContent = 'Select product and quantity to see preview';
            }
        }
    </script>
@endsection
