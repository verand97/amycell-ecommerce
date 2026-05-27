@extends('admin.layouts.app')

@section('title', 'Manajemen Produk')
@section('page-title', 'Manajemen Produk')
@section('page-subtitle', 'CRUD Inventaris & Stok Produk')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
        {{-- Search Form --}}
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                class="px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-300 placeholder-slate-500 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none">
            <select name="category_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-300 outline-none focus:ring-2 focus:ring-sky-500">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="type" onchange="this.form.submit()" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-300 outline-none focus:ring-2 focus:ring-sky-500">
                <option value="">Semua Tipe</option>
                <option value="digital" {{ request('type') === 'digital' ? 'selected' : '' }}>Digital</option>
                <option value="physical" {{ request('type') === 'physical' ? 'selected' : '' }}>Fisik</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-sky-500 text-white rounded-xl text-sm font-semibold hover:bg-sky-600 transition-colors">Cari</button>
        </form>
    </div>
    <a href="{{ route('admin.products.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-linear-to-r from-sky-500 to-indigo-600 text-white rounded-xl text-sm font-bold hover:shadow-lg transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Produk
    </a>
</div>

<div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-800">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Produk</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Kategori</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Harga</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Stok</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Tipe</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            @forelse($products as $product)
            <tr class="hover:bg-slate-800/40 transition-colors">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-xl shrink-0">
                            {{ $product->category->icon ?? '📦' }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-200 text-sm leading-tight">{{ \Illuminate\Support\Str::limit($product->name, 35) }}</p>
                            <p class="text-[11px] text-slate-500 font-mono">{{ $product->sku ?? '-' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <span class="text-xs text-slate-400">{{ $product->category->name }}</span>
                </td>
                <td class="px-4 py-4">
                    <p class="text-sm font-bold text-sky-400">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</p>
                    @if($product->sale_price)
                        <p class="text-[11px] text-slate-600 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    @endif
                </td>
                <td class="px-4 py-4">
                    @if($product->isDigital())
                        <span class="text-xs text-sky-400">∞ Digital</span>
                    @else
                        <span class="text-xs font-bold {{ $product->stock <= 5 ? 'text-red-400' : 'text-emerald-400' }}">{{ $product->stock }}</span>
                        @if($product->stock <= 5 && $product->stock > 0)
                            <span class="text-[10px] text-red-400 block">⚠️ Stok Rendah</span>
                        @endif
                    @endif
                </td>
                <td class="px-4 py-4">
                    <span class="px-2 py-1 text-[10px] font-semibold rounded-full {{ $product->isDigital() ? 'bg-sky-500/10 text-sky-400' : 'bg-emerald-500/10 text-emerald-400' }}">
                        {{ $product->isDigital() ? '⚡ Digital' : '📦 Fisik' }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $product->is_active ? 'bg-emerald-500' : 'bg-slate-700' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $product->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </form>
                </td>
                <td class="px-5 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg transition-colors">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Nonaktifkan produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs rounded-lg transition-colors">Nonaktifkan</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                    <div class="text-4xl mb-2">📦</div>
                    <p>Belum ada produk</p>
                    <a href="{{ route('admin.products.create') }}" class="text-sky-400 hover:text-sky-300 text-sm mt-2 inline-block">+ Tambah Produk</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($products->hasPages())
        <div class="px-5 py-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
