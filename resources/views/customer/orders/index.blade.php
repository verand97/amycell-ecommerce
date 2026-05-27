@extends('customer.layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-black text-slate-800 mb-6">📦 Pesanan Saya</h1>

    @if($orders->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-100">
            <div class="text-7xl mb-4">📭</div>
            <h2 class="text-xl font-bold text-slate-700">Belum ada pesanan</h2>
            <a href="{{ route('catalog') }}" class="mt-4 inline-block px-8 py-3 bg-linear-to-r from-sky-500 to-indigo-600 text-white rounded-2xl font-bold">Mulai Belanja</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                {{-- Order Header --}}
                <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3 border-b border-slate-50">
                    <div>
                        <span class="text-xs text-slate-400">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        <p class="font-mono text-sm font-bold text-slate-700 mt-0.5">{{ $order->order_number }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $statusClasses = match($order->status_color) {
                                'yellow'  => 'bg-yellow-100 text-yellow-700',
                                'blue'    => 'bg-blue-100 text-blue-700',
                                'green'   => 'bg-emerald-100 text-emerald-700',
                                'red'     => 'bg-red-100 text-red-700',
                                'indigo'  => 'bg-indigo-100 text-indigo-700',
                                'purple'  => 'bg-purple-100 text-purple-700',
                                default   => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusClasses }}">
                            {{ $order->status_label }}
                        </span>
                        <a href="{{ route('customer.orders.show', $order->id) }}" class="text-xs font-semibold text-sky-600 hover:text-indigo-600 transition-colors">Detail →</a>
                    </div>
                </div>

                {{-- Items Preview --}}
                <div class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        @foreach($order->items->take(3) as $item)
                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-lg shrink-0">
                                {{ $item->product?->category?->icon ?? '📦' }}
                            </div>
                        @endforeach
                        @if($order->items->count() > 3)
                            <span class="text-xs text-slate-400">+{{ $order->items->count() - 3 }} lainnya</span>
                        @endif
                        <div class="flex-1"></div>
                        <div class="text-right">
                            <p class="text-xs text-slate-400">Total</p>
                            <p class="font-bold text-sky-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
