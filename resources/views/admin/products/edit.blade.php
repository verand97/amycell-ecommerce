@extends('admin.layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')

        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Nama Produk *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-4 py-2.5 bg-slate-900 border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-700' }} rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none transition-all">
                @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Kategori *</label>
                <select name="category_id" required class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Tipe</label>
                <select name="type" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
                    <option value="digital" {{ $product->type === 'digital' ? 'selected' : '' }}>⚡ Digital</option>
                    <option value="physical" {{ $product->type === 'physical' ? 'selected' : '' }}>📦 Fisik</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Harga Normal *</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="500" required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Harga Promo</label>
                <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="500"
                    class="w-full px-4 py-2.5 bg-slate-900 border {{ $errors->has('sale_price') ? 'border-red-500' : 'border-slate-700' }} rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
                @error('sale_price') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Stok *</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                    class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none resize-none">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Ganti Gambar</label>
                @if($product->image)
                    <div class="mb-2 p-2 bg-slate-800 rounded-lg inline-flex items-center gap-2">
                        <img src="{{ $product->image_url }}" class="w-10 h-10 object-contain rounded">
                        <span class="text-xs text-slate-400">Gambar saat ini</span>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20 file:text-xs">
            </div>

            <div class="flex items-center gap-6 mt-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="w-4 h-4 accent-sky-500">
                    <span class="text-sm text-slate-300">Aktif</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="w-4 h-4 accent-amber-500">
                    <span class="text-sm text-slate-300">⭐ Unggulan</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-linear-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-xl hover:shadow-lg transition-all text-sm">Perbarui Produk</button>
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2.5 bg-slate-800 text-slate-300 font-semibold rounded-xl hover:bg-slate-700 transition-all text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection
