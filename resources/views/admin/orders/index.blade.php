@extends('admin.layouts.app')

@section('title', 'Manajemen Pesanan')
@section('page-title', 'Manajemen Pesanan')
@section('page-subtitle', 'Semua pesanan pelanggan')

@section('content')
{{-- Status Filter --}}
<div class="flex flex-wrap gap-2 mb-5">
    <a href="{{ route('admin.orders.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ !request('status') ? 'bg-sky-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }} transition-all">Semua ({{ $statusCounts->sum() }})</a>
    @foreach([
        'awaiting_payment' => 'Menunggu Bayar',
        'payment_uploaded' => 'Bukti Dikirim',
        'paid' => 'Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ] as $status => $label)
    <a href="{{ route('admin.orders.index') }}?status={{ $status }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('status') === $status ? 'bg-sky-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }} transition-all">
        {{ $label }} ({{ $statusCounts[$status] ?? 0 }})
    </a>
    @endforeach
</div>

{{-- Search --}}
<form method="GET" class="mb-4 flex gap-2">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan atau nama pelanggan..."
        class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl text-sm text-slate-300 placeholder-slate-500 focus:ring-2 focus:ring-sky-500 outline-none max-w-md">
    <button type="submit" class="px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-xl">Cari</button>
</form>

<div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-800">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">No. Pesanan</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Total</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Tanggal</th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-400 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            @forelse($orders as $order)
            <tr class="hover:bg-slate-800/40 transition-colors">
                <td class="px-5 py-3.5">
                    <p class="font-mono text-xs text-slate-200 font-bold">{{ $order->order_number }}</p>
                </td>
                <td class="px-4 py-3.5">
                    <p class="text-sm text-slate-300 font-medium">{{ $order->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $order->user->email }}</p>
                </td>
                <td class="px-4 py-3.5">
                    <p class="text-sm font-bold text-sky-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                </td>
                <td class="px-4 py-3.5">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full
                        @if(in_array($order->status_color, ['yellow'])) bg-yellow-500/20 text-yellow-400
                        @elseif($order->status_color === 'blue') bg-blue-500/20 text-blue-400
                        @elseif($order->status_color === 'green') bg-emerald-500/20 text-emerald-400
                        @elseif($order->status_color === 'red') bg-red-500/20 text-red-400
                        @elseif($order->status_color === 'indigo') bg-indigo-500/20 text-indigo-400
                        @elseif($order->status_color === 'purple') bg-purple-500/20 text-purple-400
                        @else bg-slate-700 text-slate-300
                        @endif">
                        {{ $order->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3.5">
                    <p class="text-xs text-slate-400">{{ $order->created_at->format('d M Y') }}</p>
                    <p class="text-[11px] text-slate-600">{{ $order->created_at->format('H:i') }}</p>
                </td>
                <td class="px-5 py-3.5 text-right">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-lg transition-colors">Detail →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">Tidak ada pesanan</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-slate-800">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
