@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dasbor Utama')
@section('page-subtitle', 'Ringkasan performa Toko Amycell')

@section('content')

{{-- Metric Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total Revenue --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-sky-500/30 transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Total Pendapatan</span>
            <div class="w-8 h-8 bg-sky-500/10 rounded-lg flex items-center justify-center">💰</div>
        </div>
        <p class="text-2xl font-black text-white">Rp {{ number_format($totalRevenue/1000000, 1, ',', '.') }}jt</p>
        <p class="text-xs text-slate-500 mt-1">Semua waktu (terverifikasi)</p>
    </div>

    {{-- Monthly Revenue --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/30 transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Bulan Ini</span>
            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">📈</div>
        </div>
        <p class="text-2xl font-black text-emerald-400">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ now()->format('F Y') }}</p>
    </div>

    {{-- Today Revenue --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-purple-500/30 transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Hari Ini</span>
            <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center">⚡</div>
        </div>
        <p class="text-2xl font-black text-purple-400">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ now()->format('d M Y') }}</p>
    </div>

    {{-- Pending Payments --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-amber-500/30 transition-all relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Menunggu Verifikasi</span>
            <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">⏳</div>
        </div>
        <p class="text-2xl font-black text-amber-400">{{ $pendingPayments }}</p>
        <a href="{{ route('admin.transactions.index') }}?status=pending" class="text-xs text-amber-500 hover:text-amber-300 transition-colors">Verifikasi Sekarang →</a>
        @if($pendingPayments > 0)
            <div class="absolute top-0 right-0 w-1 h-full bg-amber-500 rounded-r-2xl animate-pulse"></div>
        @endif
    </div>
</div>

{{-- Row 2: Charts & Stats --}}
<div class="grid lg:grid-cols-3 gap-4 mb-6">

    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-white">Grafik Pendapatan</h3>
            <div class="flex gap-2">
                <button id="tab-daily" onclick="showChart('daily')" class="px-3 py-1 text-xs font-semibold rounded-lg bg-sky-500 text-white transition-all">7 Hari</button>
                <button id="tab-monthly" onclick="showChart('monthly')" class="px-3 py-1 text-xs font-semibold rounded-lg bg-slate-800 text-slate-400 hover:bg-slate-700 transition-all">6 Bulan</button>
            </div>
        </div>
        <canvas id="revenue-chart" height="200"></canvas>
    </div>

    {{-- Stats --}}
    <div class="space-y-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-500/10 rounded-xl flex items-center justify-center text-xl">📦</div>
                <div>
                    <p class="text-xs text-slate-400">Total Pesanan</p>
                    <p class="text-xl font-black text-white">{{ number_format($totalOrders) }}</p>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-3 gap-1 text-center">
                <div class="bg-yellow-500/10 rounded-lg p-2">
                    <p class="text-sm font-bold text-yellow-400">{{ $pendingOrders }}</p>
                    <p class="text-[10px] text-slate-500">Pending</p>
                </div>
                <div class="bg-indigo-500/10 rounded-lg p-2">
                    <p class="text-sm font-bold text-indigo-400">{{ $processOrders }}</p>
                    <p class="text-[10px] text-slate-500">Proses</p>
                </div>
                <div class="bg-emerald-500/10 rounded-lg p-2">
                    <p class="text-sm font-bold text-emerald-400">{{ $completedOrders }}</p>
                    <p class="text-[10px] text-slate-500">Selesai</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-xl">👥</div>
                <div>
                    <p class="text-xs text-slate-400">Total Pelanggan</p>
                    <p class="text-xl font-black text-white">{{ number_format($totalCustomers) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $lowStockProducts > 0 ? 'red' : 'slate' }}-500/10 rounded-xl flex items-center justify-center text-xl">📋</div>
                    <div>
                        <p class="text-xs text-slate-400">Produk</p>
                        <p class="text-xl font-black text-white">{{ $totalProducts }}</p>
                    </div>
                </div>
                @if($lowStockProducts > 0)
                    <a href="{{ route('admin.products.index') }}?status=active" class="text-xs bg-red-500/10 text-red-400 px-2 py-1 rounded-lg border border-red-500/20">⚠️ {{ $lowStockProducts }} stok rendah</a>
                @endif
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-500/10 rounded-xl flex items-center justify-center text-xl">🔧</div>
                <div>
                    <p class="text-xs text-slate-400">Servis HP</p>
                    <p class="text-xl font-black text-white">{{ number_format($totalServices) }}</p>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-1 text-center">
                <div class="bg-yellow-500/10 rounded-lg p-2">
                    <p class="text-sm font-bold text-yellow-400">{{ $pendingServices }}</p>
                    <p class="text-[10px] text-slate-500">Pending</p>
                </div>
                <div class="bg-purple-500/10 rounded-lg p-2">
                    <p class="text-sm font-bold text-purple-400">{{ $activeServices }}</p>
                    <p class="text-[10px] text-slate-500">Aktif</p>
                </div>
            </div>
            @if($pendingServices > 0)
                <a href="{{ route('admin.services.index') }}?status=pending" class="mt-2 block text-center text-xs text-orange-400 hover:text-orange-300 transition-colors">Lihat Pending →</a>
            @endif
        </div>
    </div>
</div>

{{-- Row 3: Recent Orders & Top Products --}}
<div class="grid lg:grid-cols-3 gap-4">

    {{-- Recent Orders --}}
    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
            <h3 class="font-bold text-white">Pesanan Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-sky-400 hover:text-sky-300 transition-colors">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-800">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs text-slate-300">{{ $order->order_number }}</p>
                            <p class="text-[11px] text-slate-500">{{ $order->user->name }}</p>
                        </td>
                        <td class="px-3 py-3">
                            @php
                                $statusClasses = match($order->status_color) {
                                    'yellow'  => 'bg-yellow-500/20 text-yellow-400',
                                    'green'   => 'bg-emerald-500/20 text-emerald-400',
                                    'red'     => 'bg-red-500/20 text-red-400',
                                    'blue'    => 'bg-blue-500/20 text-blue-400',
                                    'indigo'  => 'bg-indigo-500/20 text-indigo-400',
                                    default   => 'bg-slate-700 text-slate-300',
                                };
                            @endphp
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full {{ $statusClasses }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-right">
                            <p class="text-xs font-bold text-sky-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-500">{{ $order->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] rounded-lg transition-colors font-medium">Detail →</a>
                                @if($order->status === 'payment_uploaded' && $order->transaction && $order->transaction->status === 'pending')
                                    <form action="{{ route('admin.transactions.verify', $order->transaction->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Verifikasi transaksi ini? Status pesanan akan otomatis diperbarui ke DIBAYAR.')"
                                            class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-bold rounded-lg transition-colors shadow-sm cursor-pointer">
                                            ✅ Verifikasi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800">
            <h3 class="font-bold text-white">Produk Terlaris</h3>
        </div>
        <div class="p-5 space-y-3">
            @foreach($topProducts as $i => $product)
            <div class="flex items-center gap-3">
                <span class="text-slate-600 font-bold text-sm w-5">{{ $i + 1 }}</span>
                <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-sm">
                    {{ $product->category->icon ?? '📦' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-300 truncate font-medium">{{ $product->name }}</p>
                    <p class="text-[10px] text-slate-500">{{ $product->order_items_sum_quantity ?? 0 }} terjual</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

<div id="revenue-chart-data" data-daily="{{ json_encode($revenueChart) }}" data-monthly="{{ json_encode($monthlyChart) }}" class="hidden"></div>

@push('scripts')
<script>
const chartDataEl = document.getElementById('revenue-chart-data');
const dailyData = JSON.parse(chartDataEl.dataset.daily);
const monthlyData = JSON.parse(chartDataEl.dataset.monthly);

let activeChart = null;

function createChart(data, label) {
    if (activeChart) activeChart.destroy();
    const ctx = document.getElementById('revenue-chart').getContext('2d');
    activeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.date || d.month),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: data.map(d => d.revenue),
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14,165,233,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#0ea5e9',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)' },
                    ticks: { color: '#64748b', callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 11 } }
                }
            }
        }
    });
}

function showChart(type) {
    document.getElementById('tab-daily').className = 'px-3 py-1 text-xs font-semibold rounded-lg transition-all ' + (type === 'daily' ? 'bg-sky-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700');
    document.getElementById('tab-monthly').className = 'px-3 py-1 text-xs font-semibold rounded-lg transition-all ' + (type === 'monthly' ? 'bg-sky-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700');
    createChart(type === 'daily' ? dailyData : monthlyData, type);
}

showChart('daily');
</script>
@endpush
