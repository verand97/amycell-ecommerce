<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Toko Amycell - Pulsa, Paket Data, Token Listrik, dan Aksesori HP terlengkap dengan harga terbaik">
    <title>@yield('title', 'Toko Amycell') - Pulsa & Digital Termurah</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body class="bg-slate-50 font-[Inter] text-slate-800 antialiased">

{{-- NAVBAR --}}
<nav id="navbar" class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-9 h-9 bg-linear-to-br from-sky-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                    <span class="text-white font-bold text-sm">AC</span>
                </div>
                <div>
                    <span class="font-bold text-slate-800 text-lg leading-none">Amycell</span>
                    <p class="text-[10px] text-sky-500 font-medium">Toko Digital Terpercaya</p>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition-all {{ request()->routeIs('home') ? 'text-sky-600 bg-sky-50' : '' }}">Beranda</a>
                <a href="{{ route('catalog') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition-all {{ request()->routeIs('catalog*') ? 'text-sky-600 bg-sky-50' : '' }}">Katalog</a>
                <a href="{{ route('service.landing') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-orange-600 hover:bg-orange-50 transition-all {{ request()->routeIs('service.landing') || request()->routeIs('customer.service*') ? 'text-orange-600 bg-orange-50' : '' }}">🔧 Servis HP</a>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-2">
                {{-- Cart --}}
                <a href="{{ route('customer.cart') }}" class="relative p-2.5 rounded-xl bg-slate-100 hover:bg-sky-100 hover:text-sky-600 transition-all group">
                    <svg class="w-5 h-5 text-slate-600 group-hover:text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5M12 17a1 1 0 102 0 1 1 0 00-2 0m-4 0a1 1 0 102 0 1 1 0 00-2 0"/></svg>
                    @php $cartCount = count(session('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-sky-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center animate-bounce-once">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-sky-50 transition-all">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-full object-cover" alt="Avatar">
                            <span class="text-sm font-medium text-slate-700 hidden sm:block max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs text-slate-500">Masuk sebagai</p>
                                <p class="text-sm font-semibold text-slate-700 truncate">{{ auth()->user()->name }}</p>
                            </div>
                            <a href="{{ route('customer.orders') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Pesanan Saya
                            </a>
                            <a href="{{ route('customer.service') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-orange-600 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Servis Saya
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-indigo-600 hover:bg-indigo-50 transition-all font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10m0-10a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2"/></svg>
                                    Admin Panel
                                </a>
                            @endif
                            <div class="border-t border-slate-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-sky-600 transition-all">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-linear-to-r from-sky-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-sky-200 transition-all hover:-translate-y-0.5">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
@if(session('success') || session('error'))
    <div id="flash-message" class="fixed top-20 right-4 z-100 max-w-sm w-full animate-slide-in">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-start gap-3 shadow-lg">
                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-emerald-800">Berhasil!</p>
                    <p class="text-xs text-emerald-600 mt-0.5">{{ session('success') }}</p>
                </div>
                <button onclick="document.getElementById('flash-message').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3 shadow-lg">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-red-800">Oops!</p>
                    <p class="text-xs text-red-600 mt-0.5">{{ session('error') }}</p>
                </div>
                <button onclick="document.getElementById('flash-message').remove()" class="ml-auto text-red-400 hover:text-red-600">✕</button>
            </div>
        @endif
    </div>
    <script>setTimeout(() => { const el = document.getElementById('flash-message'); if(el) el.remove(); }, 5000);</script>
@endif

{{-- Main Content --}}
<main>
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-slate-900 text-slate-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 bg-linear-to-br from-sky-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-sm">AC</span>
                    </div>
                    <span class="font-bold text-white text-xl">Toko Amycell</span>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed">Platform digital terpercaya untuk kebutuhan pulsa, paket data, token listrik, dan aksesori smartphone Anda. Proses cepat, harga terbaik, layanan 24/7.</p>
                <div class="flex items-center gap-2 mt-4">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="text-xs text-slate-400">Layanan aktif 24 jam</span>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Layanan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('catalog') }}?type=digital" class="text-slate-400 hover:text-sky-400 transition-colors">Produk Digital</a></li>
                    <li><a href="{{ route('catalog') }}?type=physical" class="text-slate-400 hover:text-sky-400 transition-colors">Produk Fisik</a></li>
                    <li><a href="{{ route('catalog') }}" class="text-slate-400 hover:text-sky-400 transition-colors">Semua Produk</a></li>
                    <li><a href="{{ route('service.landing') }}" class="text-slate-400 hover:text-orange-400 transition-colors">🔧 Servis HP</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Informasi</h4>
                <ul class="space-y-2 text-sm">
                    <li><span class="text-slate-400">📱 081234567890</span></li>
                    <li><span class="text-slate-400">📧 cs@amycell.id</span></li>
                    <li><span class="text-slate-400">📍 Jakarta, Indonesia</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-500 text-sm">© {{ date('Y') }} Toko Amycell. Hak Cipta Dilindungi.</p>
            <div class="flex items-center gap-4 text-sm text-slate-500">
                <span>BCA • Mandiri • BNI • GoPay</span>
            </div>
        </div>
    </div>
</footer>

{{-- Chat Widget --}}
@auth
    @if(auth()->user()->isCustomer())
        @include('customer.chat.widget')
    @endif
@endauth

<script>
// Alpine.js lightweight alternative for dropdowns
document.querySelectorAll('[x-data]').forEach(el => {
    const btn = el.querySelector('button');
    const dropdown = el.querySelector('[x-show]');
    if (btn && dropdown) {
        dropdown.style.display = 'none';
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });
        document.addEventListener('click', () => {
            dropdown.style.display = 'none';
        });
    }
});
</script>

@stack('scripts')
</body>
</html>
