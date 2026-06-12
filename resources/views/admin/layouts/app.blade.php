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
                <div class="w-9 h-9 bg-linear-to-br from-sky-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
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
                    ['route' => 'admin.services.index', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>', 'label' => 'Servis HP', 'badge' => true],
                    ['route' => 'admin.chat.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 3v-3z"/>', 'label' => 'Live Chat', 'badge' => true],
                ];
                $pendingCount = \App\Models\Transaction::where('status', 'pending')->count();
                $chatWaiting = \App\Models\ChatSession::where('status', 'waiting')->count();
                $servicePending = \App\Models\ServiceOrder::where('status', 'pending')->count();
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
                        @elseif($item['route'] === 'admin.services.index' && $servicePending > 0)
                            <span class="px-1.5 py-0.5 bg-orange-500 text-white text-[10px] font-bold rounded-full min-w-[18px] text-center">{{ $servicePending }}</span>
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
                <span class="text-emerald-400 font-medium mr-2">● Online</span>

                {{-- Admin Panel Bell Notification --}}
                <div class="relative" id="admin-panel-bell-container">
                    <button id="admin-panel-bell-btn" class="relative p-2 rounded-xl bg-slate-800 text-slate-400 hover:bg-slate-750 hover:text-sky-400 transition-all group">
                        <svg class="w-5 h-5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="admin-panel-bell-badge" class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-extrabold w-4.5 h-4.5 rounded-full flex items-center justify-center animate-pulse" style="display: none;">0</span>
                    </button>
                    {{-- Dropdown --}}
                    <div id="admin-panel-bell-dropdown" class="absolute right-0 mt-2 w-80 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl py-2 z-50 text-slate-200 hidden" style="display: none;">
                        <div class="px-4 py-2 border-b border-slate-800 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-white">Notifikasi Admin</p>
                                <p class="text-[10px] text-slate-400" id="admin-panel-bell-unread-text">0 Belum Dibaca</p>
                            </div>
                            <button id="admin-panel-bell-mark-all" class="text-xs font-semibold text-sky-400 hover:text-sky-300 transition-colors">Tandai semua dibaca</button>
                        </div>
                        <div class="max-h-72 overflow-y-auto" id="admin-panel-bell-list">
                            {{-- List items dynamically loaded --}}
                            <div class="px-4 py-8 text-center text-slate-500">
                                <svg class="w-8 h-8 mx-auto mb-2 text-slate-700 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89"/>
                                </svg>
                                <p class="text-xs">Memuat notifikasi...</p>
                            </div>
                        </div>
                    </div>
                </div>
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

<script>
// Admin Panel Notification Bell Dropdown & Polling
document.addEventListener('DOMContentLoaded', function() {
    const bellBtn = document.getElementById('admin-panel-bell-btn');
    const bellDropdown = document.getElementById('admin-panel-bell-dropdown');
    const bellBadge = document.getElementById('admin-panel-bell-badge');
    const bellUnreadText = document.getElementById('admin-panel-bell-unread-text');
    const bellList = document.getElementById('admin-panel-bell-list');
    const markAllBtn = document.getElementById('admin-panel-bell-mark-all');

    if (!bellBtn) return;

    // Toggle dropdown
    bellBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isHidden = bellDropdown.style.display === 'none';
        bellDropdown.style.display = isHidden ? 'block' : 'none';
    });

    // Close on click outside
    document.addEventListener('click', function() {
        if (bellDropdown) bellDropdown.style.display = 'none';
    });

    bellDropdown.addEventListener('click', function(e) {
        e.stopPropagation(); // prevent closing
    });

    // Fetch Notifications
    function fetchNotifications() {
        fetch('/admin/notifications')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Update badge
                if (data.unread_count > 0) {
                    bellBadge.textContent = data.unread_count;
                    bellBadge.style.display = 'flex';
                    bellUnreadText.textContent = data.unread_count + ' Belum Dibaca';
                } else {
                    bellBadge.style.display = 'none';
                    bellUnreadText.textContent = 'Tidak ada notifikasi baru';
                }

                // Render list items
                if (data.notifications.length === 0) {
                    bellList.innerHTML = `
                        <div class="px-4 py-8 text-center text-slate-500">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-xs text-slate-400">Belum ada notifikasi baru</p>
                        </div>
                    `;
                } else {
                    let html = '';
                    data.notifications.forEach(notif => {
                        const iconBg = notif.type === 'order' ? 'bg-sky-500/10 text-sky-400' : 'bg-orange-500/10 text-orange-400';
                        const iconSvg = notif.type === 'order' 
                            ? `<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`
                            : `<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
                        
                        const unreadDot = notif.read_at === null 
                            ? `<span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0 self-center"></span>` 
                            : '';
                        
                        const itemClass = notif.read_at === null ? 'bg-slate-800/40 hover:bg-slate-800/80' : 'hover:bg-slate-800/20';

                        html += `
                            <a href="${notif.link}" class="flex items-start gap-3 px-4 py-3 border-b border-slate-800/50 transition-all ${itemClass}">
                                <div class="w-8 h-8 rounded-xl ${iconBg} flex items-center justify-center shrink-0">
                                    ${iconSvg}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-200 truncate">${notif.title}</p>
                                    <p class="text-[11px] text-slate-400 line-clamp-2 mt-0.5">${notif.message}</p>
                                    <span class="text-[9px] text-slate-500 mt-1 block">${notif.created_at_human}</span>
                                </div>
                                ${unreadDot}
                            </a>
                        `;
                    });
                    bellList.innerHTML = html;
                }
            })
            .catch(err => console.error('[Amycell] Error fetching notifications:', err));
    }

    // Mark all as read
    markAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch('/admin/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        })
        .catch(err => console.error('[Amycell] Error marking all read:', err));
    });

    // Initial load & Poll every 30 seconds
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
});
</script>

@stack('scripts')
</body>
</html>
