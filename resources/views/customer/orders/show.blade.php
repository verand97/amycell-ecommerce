@extends('customer.layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8" id="order-details-container" data-user-id="{{ auth()->id() }}" data-order-id="{{ $order->id }}">

    {{-- Back --}}
    <a href="{{ route('customer.orders') }}" class="flex items-center gap-2 text-slate-400 hover:text-sky-600 transition-colors mb-6 text-sm">
        ← Kembali ke Pesanan
    </a>

    {{-- Order Header --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm mb-4">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs text-slate-400 mb-1">Nomor Pesanan</p>
                <p class="font-mono text-xl font-black text-slate-800">{{ $order->order_number }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $order->created_at->format('d F Y, H:i') }}</p>
            </div>
            <div class="text-right">
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
                <span id="order-status-badge"
                    class="px-4 py-1.5 text-sm font-bold rounded-full {{ $statusClasses }}">
                    <span id="order-status-text">{{ $order->status_label }}</span>
                </span>
                @if(in_array($order->status, ['pending', 'awaiting_payment']))
                    <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" onclick="return confirm('Batalkan pesanan ini?')"
                            class="text-xs text-red-500 hover:text-red-700 transition-colors">✕ Batalkan Pesanan</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Tracker --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm mb-4">
        <h3 class="font-bold text-slate-700 mb-5">Status Pengiriman</h3>
        @php
            $steps = [
                ['key' => ['pending', 'awaiting_payment', 'payment_uploaded', 'paid', 'processing', 'shipped', 'completed'], 'label' => 'Pesanan Dibuat', 'icon' => '📝'],
                ['key' => ['payment_uploaded', 'paid', 'processing', 'shipped', 'completed'], 'label' => 'Pembayaran Dikirim', 'icon' => '💳'],
                ['key' => ['paid', 'processing', 'shipped', 'completed'], 'label' => 'Pembayaran Dikonfirmasi', 'icon' => '✅'],
                ['key' => ['processing', 'shipped', 'completed'], 'label' => 'Diproses', 'icon' => '⚙️'],
                ['key' => ['shipped', 'completed'], 'label' => 'Dikirim', 'icon' => '🚚'],
                ['key' => ['completed'], 'label' => 'Selesai', 'icon' => '🎉'],
            ];
        @endphp
        <div class="space-y-4">
            @foreach($steps as $i => $step)
                @php $active = in_array($order->status, $step['key']); @endphp
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-base
                        {{ $active ? 'bg-sky-500 shadow-lg shadow-sky-200' : 'bg-slate-100' }}">
                        {{ $step['icon'] }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold {{ $active ? 'text-slate-800' : 'text-slate-400' }}">{{ $step['label'] }}</p>
                    </div>
                    @if($active)
                        <span class="w-2 h-2 bg-sky-500 rounded-full animate-pulse"></span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Midtrans Snap Payment (if needed) --}}
    @if($order->status === 'awaiting_payment')
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-4">
            <h3 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                💳 Selesaikan Pembayaran
            </h3>
            <p class="text-xs text-slate-500 mb-4">Silakan selesaikan pembayaran pesanan Anda secara aman via Midtrans menggunakan Virtual Account, QRIS, E-Wallet, atau Kartu Kredit.</p>

            @if($order->snap_token)
                <button id="pay-button" class="w-full py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl transition-colors text-sm cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    Bayar Sekarang
                </button>
            @else
                <div class="p-3 bg-red-50 text-red-600 rounded-xl text-xs font-semibold text-center">
                    Gagal memuat sesi pembayaran Midtrans. Silakan hubungi admin atau muat ulang halaman.
                </div>
            @endif
        </div>
    @endif

    {{-- Items --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm mb-4">
        <h3 class="font-bold text-slate-700 mb-4">Detail Produk</h3>
        <div class="space-y-3">
            @foreach($order->items as $item)
            <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-2xl shrink-0">
                    {{ $item->product?->category?->icon ?? '📦' }}
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-700">{{ $item->product_name }}</p>
                    <p class="text-xs text-slate-400">Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                </div>
                <p class="font-bold text-slate-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
            <div class="flex justify-between text-sm text-slate-500"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            @if($order->shipping_cost > 0)
                <div class="flex justify-between text-sm text-slate-500"><span>Ongkir</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
            @endif
            <div class="flex justify-between font-bold text-slate-800 text-base pt-2 border-t border-slate-100"><span>Total</span><span class="text-sky-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
        </div>
    </div>

    {{-- Payment Proof --}}
    @if($order->payment_proof_url)
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <h3 class="font-bold text-slate-700 mb-3">Bukti Pembayaran</h3>
            <img src="{{ $order->payment_proof_url }}" class="max-h-64 rounded-xl object-contain mx-auto" alt="Bukti Bayar">
            @if($order->transaction)
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs font-semibold">Status Verifikasi:</span>
                    <span class="text-xs px-2 py-1 rounded-full font-bold
                        {{ $order->transaction->status === 'verified' ? 'bg-emerald-100 text-emerald-700' : ($order->transaction->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $order->transaction->status_label }}
                    </span>
                </div>
            @endif
        </div>
    @endif
</div>

@push('scripts')
@if($order->status === 'awaiting_payment' && $order->snap_token)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.addEventListener('click', function () {
                window.snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function(result){
                        window.location.reload();
                    },
                    onPending: function(result){
                        window.location.reload();
                    },
                    onError: function(result){
                        alert("Pembayaran gagal! Silakan coba lagi.");
                    },
                    onClose: function(){
                        alert('Anda menutup popup pembayaran sebelum menyelesaikan transaksi.');
                    }
                });
            });
        }
    });
</script>
@endif
<script>
// Real-time status tracking via Reverb
const orderContainer = document.getElementById('order-details-container');
if (orderContainer) {
    const userId = parseInt(orderContainer.dataset.userId) || null;
    const orderId = parseInt(orderContainer.dataset.orderId) || null;

    if (userId && orderId && typeof Echo !== 'undefined') {
        Echo.private(`orders.${userId}`)
            .listen('.order.status.updated', (data) => {
                if (data.order_id === orderId) {
                    const statusText = document.getElementById('order-status-text');
                    if (statusText) statusText.textContent = data.status_label;
                    // Show notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-20 right-4 z-50 bg-sky-500 text-white px-5 py-3 rounded-2xl shadow-2xl text-sm font-semibold animate-slide-in';
                    notification.textContent = `🔔 Status pesanan diperbarui: ${data.status_label}`;
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 5000);
                }
            });
    }
}
</script>
@endpush

@endsection
