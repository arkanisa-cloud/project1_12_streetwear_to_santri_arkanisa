@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('admin.products.index') }}"
                class="text-xs font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-950 transition-colors flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Inventory
            </a>
            <h1 class="text-3xl font-black tracking-tighter text-zinc-950 uppercase italic">Add New Collection</h1>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-5">
                        <h2
                            class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400 mb-4 border-b border-zinc-50 pb-2">
                            Basic Information</h2>

                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-2 block">Product
                                Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="e.g. Boxy Heavyweight Tee"
                                class="w-full bg-zinc-50 border-none rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-zinc-950 placeholder:text-zinc-300">
                            @error('name')
                                <p class="text-[10px] text-red-600 font-bold uppercase mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-2 block">Category</label>
                                <select name="category_id"
                                    class="w-full bg-zinc-50 border-none rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-zinc-950">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-2 block">Price
                                    (IDR)</label>
                                <input type="number" name="price" value="{{ old('price') }}" placeholder="0"
                                    class="w-full bg-zinc-50 border-none rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-zinc-950">
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-2 block">Weight
                                    (Gram)</label>
                                <input type="number" name="weight" value="{{ old('weight', 500) }}" placeholder="500"
                                    class="w-full bg-zinc-50 border-none rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-zinc-950">
                            </div>
                        </div>

                        <div>
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-2 block">Description</label>
                            <textarea name="description" rows="5" placeholder="Detail material, sizing, etc..."
                                class="w-full bg-zinc-50 border-none rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-zinc-950 placeholder:text-zinc-300">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <h2
                        class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400 mb-4 border-b border-zinc-50 pb-2">
                        Media</h2>
                    <div class="grid grid-cols-2 gap-4">
                        {{-- POV Depan --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-950 block">POV
                                Depan</label>
                            <div id="front-image-preview"
                                class="w-full aspect-square bg-zinc-50 rounded-xl border-2 border-dashed border-zinc-200 flex items-center justify-center overflow-hidden">
                                <span
                                    class="text-[10px] font-black text-zinc-300 uppercase tracking-widest text-center px-2">No
                                    Image</span>
                            </div>
                            <input type="file" name="image" id="front-image-input" class="hidden" accept="image/*">
                            <button type="button" onclick="document.getElementById('front-image-input').click()"
                                class="w-full py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                Upload Depan
                            </button>
                        </div>

                        {{-- POV Belakang --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-950 block">POV
                                Belakang</label>
                            <div id="back-image-preview"
                                class="w-full aspect-square bg-zinc-50 rounded-xl border-2 border-dashed border-zinc-200 flex items-center justify-center overflow-hidden">
                                <span
                                    class="text-[10px] font-black text-zinc-300 uppercase tracking-widest text-center px-2">No
                                    Image</span>
                            </div>
                            <input type="file" name="back_image" id="back-image-input" class="hidden" accept="image/*">
                            <button type="button" onclick="document.getElementById('back-image-input').click()"
                                class="w-full py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                Upload Belakang
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-black uppercase tracking-[0.3em] rounded-2xl shadow-xl shadow-zinc-950/20 transition-all">
                    Publish Item
                </button>
            </div>
    </div>
    </form>
    </div>

    <script>
        // Preview POV Depan
        document.getElementById('front-image-input').onchange = evt => {
            const [file] = evt.target.files;
            if (file) {
                const preview = document.getElementById('front-image-preview');
                preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover">`;
                preview.classList.remove('border-dashed');
            }
        }

        // Preview POV Belakang
        document.getElementById('back-image-input').onchange = evt => {
            const [file] = evt.target.files;
            if (file) {
                const preview = document.getElementById('back-image-preview');
                preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover">`;
                preview.classList.remove('border-dashed');
            }
        }
    </script>
@endsection
