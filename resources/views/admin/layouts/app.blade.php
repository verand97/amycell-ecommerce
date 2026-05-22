<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Amycell Admin Panel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body class="bg-slate-950 font-[Inter] text-slate-100 h-full">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside id="sidebar" class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0 transition-all duration-300">

        {{-- Logo --}}
        <div class="p-5 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 bg-gradient-to-br from-sky-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-sm">AC</span>
                </div>
                <div>
                    <p class="font-black text-white leading-none text-sm">Amycell Admin</p>
                    <p class="text-[10px] text-sky-400 font-medium mt-0.5">Panel Manajemen</p>
                </div>
            </div>
        </div>

        {{-- Admin Info --}}
        <div class="px-5 py-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full shrink-0 object-cover" alt="Avatar">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-200 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-emerald-400 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Administrator
                    </p>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            @php
                $navItems = [
                    ['route' => 'admin.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0v10m0-10a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2"/>', 'label' => 'Dasbor'],
                    ['route' => 'admin.products.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>', 'label' => 'Produk'],
                    ['route' => 'admin.categories.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>', 'label' => 'Kategori'],
                    ['route' => 'admin.orders.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>', 'label' => 'Pesanan'],
                    ['route' => 'admin.transactions.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'label' => 'Transaksi', 'badge' => true],
                    ['route' => 'admin.chat.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z"/>', 'label' => 'Live Chat', 'badge' => true],
                ];
                $pendingCount = \App\Models\Transaction::where('status', 'pending')->count();
                $chatWaiting = \App\Models\ChatSession::where('status', 'waiting')->count();
            @endphp

            @foreach($navItems as $item)
                @php $isActive = request()->routeIs($item['route'] . '*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group
                          {{ $isActive ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <svg class="w-4.5 h-4.5 shrink-0 {{ $isActive ? 'text-sky-400' : 'text-slate-500 group-hover:text-slate-300' }}" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                    <span class="text-sm font-medium flex-1">{{ $item['label'] }}</span>
                    @if(isset($item['badge']))
                        @if($item['route'] === 'admin.transactions.index' && $pendingCount > 0)
                            <span class="px-1.5 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] text-center">{{ $pendingCount }}</span>
                        @elseif($item['route'] === 'admin.chat.dashboard' && $chatWaiting > 0)
                            <span class="px-1.5 py-0.5 bg-amber-500 text-white text-[10px] font-bold rounded-full min-w-[18px] text-center">{{ $chatWaiting }}</span>
                        @endif
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Sidebar Footer --}}
        <div class="p-4 border-t border-slate-800">
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-500 hover:text-sky-400 transition-colors rounded-lg hover:bg-slate-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Lihat Toko
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-slate-500 hover:text-red-400 transition-colors rounded-lg hover:bg-slate-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top Bar --}}
        <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex items-center justify-between shrink-0">
            <div>
                <h1 class="text-lg font-bold text-white">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-slate-400">@yield('page-subtitle', 'Selamat datang di Admin Panel Amycell')</p>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span>{{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                <span class="w-px h-4 bg-slate-700"></span>
                <span class="text-emerald-400 font-medium">● Online</span>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto bg-slate-950 p-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 flex items-center gap-3 px-5 py-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 flex items-center gap-3 px-5 py-3.5 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
