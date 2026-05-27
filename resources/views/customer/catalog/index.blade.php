@extends('customer.layouts.app')

@section('title', 'Katalog Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-800">Katalog Produk</h1>
        <p class="text-slate-500 mt-1">{{ $products->total() }} produk tersedia</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Sidebar Filter --}}
        <aside class="lg:w-64 shrink-0">
            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm sticky top-20">
                <h3 class="font-bold text-slate-700 mb-4">Filter Produk</h3>
                <form method="GET" action="{{ route('catalog') }}" id="filter-form">

                    {{-- Search --}}
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide block mb-2">Cari</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Nama produk..."
                                class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                        </div>
                    </div>

                    {{-- Categories --}}
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide block mb-2">Kategori</label>
                        <div class="space-y-1.5 max-h-52 overflow-y-auto">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()" class="accent-sky-500">
                                <span class="text-sm text-slate-600 group-hover:text-sky-600">Semua Kategori</span>
                            </label>
                            @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()" class="accent-sky-500">
                                <span class="text-sm text-slate-600 group-hover:text-sky-600">{{ $cat->icon }} {{ $cat->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Type --}}
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide block mb-2">Tipe</label>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="" {{ !request('type') ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()" class="accent-sky-500">
                                <span class="text-sm text-slate-600">Semua</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="digital" {{ request('type') === 'digital' ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()" class="accent-sky-500">
                                <span class="text-sm text-slate-600">⚡ Digital</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="physical" {{ request('type') === 'physical' ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()" class="accent-sky-500">
                                <span class="text-sm text-slate-600">📦 Fisik</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-linear-to-r from-sky-500 to-indigo-600 text-white rounded-xl text-sm font-bold hover:shadow-lg transition-all">Terapkan</button>
                    @if(request()->anyFilled(['search', 'category', 'type', 'sort']))
                        <a href="{{ route('catalog') }}" class="block text-center text-xs text-red-500 hover:text-red-700 mt-2 transition-colors">✕ Hapus Filter</a>
                    @endif
                </form>
            </div>
        </aside>

        {{-- Products Grid --}}
        <div class="flex-1">
            {{-- Sort --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-slate-500">Menampilkan <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong> dari <strong>{{ $products->total() }}</strong></p>
                <select name="sort" onchange="window.location.href=this.value"
                        class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-sky-500 outline-none">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort','newest') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-lg font-bold text-slate-700 mb-2">Produk tidak ditemukan</h3>
                    <p class="text-slate-500 text-sm">Coba ubah filter atau kata kunci pencarian Anda.</p>
                    <a href="{{ route('catalog') }}" class="mt-4 inline-block px-6 py-2.5 bg-sky-500 text-white rounded-xl text-sm font-semibold hover:bg-sky-600 transition-colors">Lihat Semua Produk</a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        @include('customer.catalog.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
