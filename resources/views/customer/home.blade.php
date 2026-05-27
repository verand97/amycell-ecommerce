@extends('customer.layouts.app')

@section('title', 'Beranda')

@section('content')

{{-- Hero Section --}}
<section class="relative overflow-hidden bg-linear-to-br from-slate-900 via-sky-950 to-indigo-950 text-white min-h-[85vh] flex items-center">
    {{-- Background decorations --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-72 h-72 bg-sky-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-sky-400/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 py-20 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-sky-500/20 border border-sky-400/30 rounded-full text-sky-300 text-sm font-medium mb-6 backdrop-blur-sm">
            <span class="w-2 h-2 bg-sky-400 rounded-full animate-pulse"></span>
            Proses Instan • Harga Terbaik • Terpercaya
        </div>

        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-tight mb-6">
            Toko Digital
            <span class="bg-linear-to-r from-sky-400 to-indigo-400 bg-clip-text text-transparent block">Terlengkap</span>
        </h1>
        <p class="text-slate-300 text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
            Pulsa, paket data, token listrik, voucher game, dan aksesori HP tersedia dengan harga kompetitif dan proses kilat.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
            <a href="{{ route('catalog') }}" class="px-8 py-4 bg-linear-to-r from-sky-500 to-indigo-600 rounded-2xl text-white font-bold text-lg hover:shadow-2xl hover:shadow-sky-500/30 transition-all hover:-translate-y-1 hover:scale-105">
                🛒 Belanja Sekarang
            </a>
            <a href="{{ route('catalog') }}?type=digital" class="px-8 py-4 bg-white/10 border border-white/20 rounded-2xl text-white font-semibold text-lg hover:bg-white/20 transition-all backdrop-blur-sm">
                ⚡ Produk Digital
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 max-w-lg mx-auto">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4">
                <p class="text-2xl font-black text-sky-400">1K+</p>
                <p class="text-slate-400 text-xs mt-1">Pelanggan</p>
            </div>
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4">
                <p class="text-2xl font-black text-indigo-400">50+</p>
                <p class="text-slate-400 text-xs mt-1">Produk</p>
            </div>
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-4">
                <p class="text-2xl font-black text-emerald-400">24/7</p>
                <p class="text-slate-400 text-xs mt-1">Layanan</p>
            </div>
        </div>
    </div>
</section>

{{-- Categories --}}
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-black text-slate-800">Kategori Produk</h2>
        <p class="text-slate-500 mt-2">Temukan semua kebutuhan digitalmu</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
        @foreach($categories as $category)
        <a href="{{ route('catalog') }}?category={{ $category->slug }}" class="group flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-slate-100 hover:border-sky-200 hover:shadow-lg hover:shadow-sky-100 transition-all hover:-translate-y-1">
            <div class="text-3xl group-hover:scale-110 transition-transform">{{ $category->icon }}</div>
            <span class="text-xs font-semibold text-slate-600 group-hover:text-sky-600 text-center leading-tight">{{ $category->name }}</span>
            <span class="text-[10px] text-slate-400">{{ $category->active_products_count }} produk</span>
        </a>
        @endforeach
    </div>
</section>

{{-- Featured Products --}}
@if($featuredProducts->count() > 0)
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-800">⭐ Produk Unggulan</h2>
            <p class="text-slate-500 mt-1">Pilihan terlaris pelanggan Amycell</p>
        </div>
        <a href="{{ route('catalog') }}" class="text-sky-600 font-semibold hover:text-indigo-600 transition-colors text-sm">Lihat Semua →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($featuredProducts as $product)
            @include('customer.catalog.partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

{{-- Latest Products --}}
<section class="bg-linear-to-br from-slate-800 to-slate-900 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-black text-white">🆕 Produk Terbaru</h2>
                <p class="text-slate-400 mt-1">Update harga & stok realtime</p>
            </div>
            <a href="{{ route('catalog') }}" class="text-sky-400 font-semibold hover:text-sky-300 transition-colors text-sm">Lihat Semua →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($latestProducts as $product)
                @include('customer.catalog.partials.product-card', ['product' => $product, 'dark' => true])
            @endforeach
        </div>
    </div>
</section>

{{-- Service HP CTA --}}
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="bg-linear-to-r from-orange-500 via-red-500 to-pink-600 rounded-3xl p-8 md:p-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMTAiIGN5PSIxMCIgcj0iMS41IiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMSkiLz48L3N2Zz4=')] opacity-50"></div>
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-white">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-xs font-semibold mb-3 backdrop-blur-sm">
                    🔧 LAYANAN BARU
                </div>
                <h2 class="text-3xl md:text-4xl font-black mb-3">Jasa Servis Smartphone</h2>
                <p class="text-orange-100 text-lg max-w-lg">HP rusak? Kami siap membantu! Perbaikan LCD, baterai, software, dan lainnya oleh teknisi berpengalaman.</p>
            </div>
            <a href="{{ route('service.landing') }}" class="shrink-0 px-8 py-4 bg-white text-orange-600 font-bold text-lg rounded-2xl hover:shadow-2xl hover:shadow-white/20 transition-all hover:-translate-y-1 hover:scale-105">
                🔧 Pelajari Lebih Lanjut
            </a>
        </div>
    </div>
</section>
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-black text-slate-800">Kenapa Pilih Amycell?</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="text-center p-8 bg-white rounded-3xl border border-slate-100 hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="w-16 h-16 bg-linear-to-br from-sky-400 to-indigo-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">⚡</div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">Proses Instan</h3>
            <p class="text-slate-500 text-sm">Produk digital dikirim otomatis dalam hitungan detik setelah pembayaran dikonfirmasi.</p>
        </div>
        <div class="text-center p-8 bg-white rounded-3xl border border-slate-100 hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="w-16 h-16 bg-linear-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">💰</div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">Harga Termurah</h3>
            <p class="text-slate-500 text-sm">Kami berkomitmen memberikan harga terbaik dengan update otomatis setiap hari.</p>
        </div>
        <div class="text-center p-8 bg-white rounded-3xl border border-slate-100 hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="w-16 h-16 bg-linear-to-br from-purple-400 to-pink-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">🛡️</div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">100% Aman</h3>
            <p class="text-slate-500 text-sm">Transaksi terlindungi dan dijamin. Tim CS siap membantu 24/7 melalui live chat.</p>
        </div>
    </div>
</section>

@endsection
