@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('admin.categories.index') }}"
                class="text-xs font-black uppercase tracking-widest text-zinc-400 hover:text-zinc-950 transition-colors flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Categories
            </a>
            <h1 class="text-3xl font-black tracking-tighter text-zinc-950 uppercase italic">Edit Category</h1>
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Updating: {{ $category->name }}</p>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-2 block">Category
                        Name</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                        class="w-full bg-zinc-50 border-none rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-zinc-950">
                </div>

                <div>
                    <label
                        class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-2 block">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full bg-zinc-50 border-none rounded-xl p-4 text-sm font-bold focus:ring-2 focus:ring-zinc-950">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="p-4 bg-zinc-50 rounded-xl border border-dashed border-zinc-200">
                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-tighter italic text-center">
                        This category currently holds <span class="text-zinc-950">{{ $category->products->count() }}</span>
                        products.
                    </p>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-950 mb-3 block">Visibility
                        Status</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer group">
                            <input type="radio" name="status" value="active" class="hidden peer"
                                {{ $category->status === 'active' ? 'checked' : '' }}>
                            <div
                                class="p-4 rounded-xl bg-zinc-50 border-2 border-transparent peer-checked:border-zinc-950 peer-checked:bg-white transition-all text-center">
                                <span
                                    class="text-xs font-bold uppercase tracking-widest text-zinc-400 peer-checked:text-zinc-950">Active</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer group">
                            <input type="radio" name="status" value="inactive" class="hidden peer"
                                {{ $category->status === 'inactive' ? 'checked' : '' }}>
                            <div
                                class="p-4 rounded-xl bg-zinc-50 border-2 border-transparent peer-checked:border-zinc-950 peer-checked:bg-white transition-all text-center">
                                <span
                                    class="text-xs font-bold uppercase tracking-widest text-zinc-400 peer-checked:text-zinc-950">Inactive</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-black uppercase tracking-[0.3em] rounded-2xl shadow-xl shadow-zinc-950/20 transition-all">
                Update Category
            </button>
        </form>
    </div>
@endsection
