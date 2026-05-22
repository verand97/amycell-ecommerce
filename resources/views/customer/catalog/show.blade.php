@extends('customer.layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-sky-600 transition-colors">Beranda</a>
        <span>›</span>
        <a href="{{ route('catalog') }}" class="hover:text-sky-600 transition-colors">Katalog</a>
        <span>›</span>
        <a href="{{ route('catalog') }}?category={{ $product->category->slug }}" class="hover:text-sky-600 transition-colors">{{ $product->category->name }}</a>
        <span>›</span>
        <span class="text-slate-600 truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-8 mb-12">

        {{-- Product Image --}}
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-3xl p-8 flex items-center justify-center min-h-[320px] relative">
            @if($product->is_featured)
                <div class="absolute top-4 left-4 bg-amber-400 text-amber-900 text-xs font-bold px-3 py-1 rounded-full">⭐ Unggulan</div>
            @endif
            @if($product->discount_percentage)
                <div class="absolute top-4 right-4 bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-full">-{{ $product->discount_percentage }}%</div>
            @endif
            @if($product->image)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-64 object-contain">
            @else
                <div class="text-8xl opacity-60">{{ $product->category->icon ?? '📦' }}</div>
            @endif
        </div>

        {{-- Product Details --}}
        <div>
            <span class="text-sm font-semibold text-sky-600 bg-sky-50 px-3 py-1 rounded-full">{{ $product->category->name }}</span>

            <h1 class="text-2xl font-black text-slate-800 mt-3 leading-tight">{{ $product->name }}</h1>

            <div class="flex items-center gap-3 mt-2">
                <span class="text-xs text-slate-500">{{ $product->sold_count }} terjual</span>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $product->type === 'digital' ? 'bg-sky-100 text-sky-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ $product->type === 'digital' ? '⚡ Digital' : '📦 Fisik' }}
                </span>
            </div>

            {{-- Price --}}
            <div class="mt-5 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                @if($product->sale_price)
                    <p class="text-sm text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <p class="text-3xl font-black text-sky-600">Rp {{ number_format($product->sale_price, 0, ',', '.') }}</p>
                    <p class="text-xs text-red-500 font-semibold mt-1">Hemat Rp {{ number_format($product->price - $product->sale_price, 0, ',', '.') }}</p>
                @else
                    <p class="text-3xl font-black text-sky-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                @endif
            </div>

            {{-- Stock --}}
            <div class="mt-4 flex items-center gap-2">
                @if($product->isInStock())
                    <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                    <span class="text-sm text-emerald-600 font-medium">
                        {{ $product->isDigital() ? 'Tersedia' : "Stok: {$product->stock} unit" }}
                    </span>
                @else
                    <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                    <span class="text-sm text-red-500 font-medium">Stok habis</span>
                @endif
            </div>

            {{-- Description --}}
            @if($product->description)
                <p class="mt-4 text-slate-600 text-sm leading-relaxed">{{ $product->description }}</p>
            @endif

            {{-- Add to Cart --}}
            @auth
                @if($product->isInStock())
                    <form action="{{ route('customer.cart.add', $product->id) }}" method="POST" class="mt-6 flex items-center gap-3">
                        @csrf
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden">
                            <button type="button" onclick="decQty()" class="px-3 py-2.5 hover:bg-slate-100 text-slate-600 transition-colors">−</button>
                            <input type="number" name="quantity" id="qty" value="1" min="1" max="{{ $product->isDigital() ? 10 : $product->stock }}"
                                class="w-14 text-center border-x border-slate-200 py-2.5 text-sm font-bold focus:outline-none">
                            <button type="button" onclick="incQty()" class="px-3 py-2.5 hover:bg-slate-100 text-slate-600 transition-colors">+</button>
                        </div>
                        <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sky-200 transition-all hover:-translate-y-0.5">
                            🛒 Tambah ke Keranjang
                        </button>
                    </form>
                @else
                    <div class="mt-6 py-3 bg-slate-100 text-slate-400 text-center rounded-xl font-semibold">Stok Habis</div>
                @endif
            @else
                <a href="{{ route('login') }}" class="mt-6 flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-xl hover:shadow-lg transition-all">
                    🔐 Login untuk Membeli
                </a>
            @endauth

            {{-- Info badges --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="text-xs px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full">✓ Transaksi Aman</span>
                <span class="text-xs px-3 py-1.5 bg-sky-50 text-sky-700 rounded-full">✓ Harga Terbaik</span>
                @if($product->isDigital())
                    <span class="text-xs px-3 py-1.5 bg-purple-50 text-purple-700 rounded-full">✓ Kirim Instan</span>
                @else
                    <span class="text-xs px-3 py-1.5 bg-amber-50 text-amber-700 rounded-full">✓ Pengiriman Terpercaya</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Full Description --}}
    @if($product->full_description)
        <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-8">
            <h2 class="font-bold text-slate-800 text-lg mb-4">Deskripsi Produk</h2>
            <div class="text-slate-600 text-sm leading-relaxed prose max-w-none">
                {!! nl2br(e($product->full_description)) !!}
            </div>
        </div>
    @endif

    {{-- Related Products --}}
    @if($related->count() > 0)
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4">Produk Serupa</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($related as $rel)
                    @include('customer.catalog.partials.product-card', ['product' => $rel])
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function decQty() {
    const inp = document.getElementById('qty');
    if (parseInt(inp.value) > 1) inp.value = parseInt(inp.value) - 1;
}
function incQty() {
    const inp = document.getElementById('qty');
    inp.value = parseInt(inp.value) + 1;
}
</script>
@endpush
@endsection
