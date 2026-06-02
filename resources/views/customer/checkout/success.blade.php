@extends('customer.layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 text-center">

    {{-- Success/Pending Icon & Titles --}}
    @if($order->status === 'paid')
        <div class="w-24 h-24 bg-linear-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-emerald-200 animate-bounce">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl font-black text-slate-800 mb-2">Pembayaran Berhasil! 🎉</h1>
        <p class="text-slate-500 mb-8">Terima kasih, pembayaran Anda telah diterima dan pesanan sedang diproses.</p>
    @else
        <div class="w-24 h-24 bg-linear-to-br from-sky-400 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-sky-200 animate-pulse">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h1 class="text-3xl font-black text-slate-800 mb-2">Pesanan Berhasil Dibuat! 📝</h1>
        <p class="text-slate-500 mb-8">Silakan selesaikan pembayaran di bawah ini agar pesanan Anda dapat diproses.</p>
    @endif

    {{-- Order Info --}}
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm text-left mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-700">Detail Pesanan</h2>
            <span class="text-sm font-mono bg-sky-50 text-sky-700 px-3 py-1 rounded-full">{{ $order->order_number }}</span>
        </div>

        {{-- Items --}}
        <div class="space-y-3 mb-4">
            @foreach($order->items as $item)
            <div class="flex items-center gap-3 py-2 border-b border-slate-50 last:border-0">
                <span class="text-xl">{{ ($item->product?->type ?? 'physical') === 'digital' ? '⚡' : '📦' }}</span>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-700">{{ $item->product_name }}</p>
                    <p class="text-xs text-slate-400">× {{ $item->quantity }}</p>
                </div>
                <p class="text-sm font-bold text-slate-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>

        <div class="flex justify-between font-bold text-slate-800 pt-2">
            <span>Total Pembayaran</span>
            <span class="text-sky-600 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Midtrans Snap Payment --}}
    <div class="bg-slate-50 border border-slate-200 rounded-3xl p-6 mb-6 text-left">
        @if($order->status === 'awaiting_payment')
            <h3 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                💳 Selesaikan Pembayaran
            </h3>
            <p class="text-xs text-slate-505 mb-4">Silakan klik tombol di bawah untuk membayar secara aman via Midtrans menggunakan Virtual Account, QRIS, E-Wallet, atau Kartu Kredit.</p>

            @if($order->snap_token)
                <button id="pay-button" class="w-full py-3.5 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-2xl transition-all text-sm cursor-pointer shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    Bayar Sekarang
                </button>
            @else
                <div class="p-3 bg-red-50 text-red-600 rounded-xl text-xs font-semibold text-center">
                    Gagal memuat sesi pembayaran Midtrans. Silakan muat ulang halaman ini.
                </div>
            @endif
        @else
            <div class="flex items-center gap-3 text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-2xl p-4">
                <span class="text-2xl">✅</span>
                <div>
                    <h4 class="font-bold text-sm">Pembayaran Berhasil</h4>
                    <p class="text-xs text-emerald-600">Terima kasih, pembayaran Anda telah diterima dan pesanan sedang diproses.</p>
                </div>
            </div>
        @endif
    </div>

    @if($order->status === 'awaiting_payment' && $order->snap_token)
    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        const payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $order->snap_token }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('customer.orders.show', $order->id) }}";
                },
                onPending: function(result){
                    window.location.href = "{{ route('customer.orders.show', $order->id) }}";
                },
                onError: function(result){
                    alert("Pembayaran gagal! Silakan coba lagi.");
                },
                onClose: function(){
                    alert('Anda menutup popup pembayaran sebelum menyelesaikan transaksi.');
                }
            });
        });
    </script>
    @endpush
    @endif

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('customer.orders.show', $order->id) }}" class="px-6 py-3 bg-linear-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-lg transition-all">
            Pantau Status Pesanan
        </a>
        <a href="{{ route('catalog') }}" class="px-6 py-3 bg-slate-100 text-slate-600 font-semibold rounded-2xl hover:bg-slate-200 transition-all">
            Lanjut Belanja
        </a>
    </div>
</div>
@endsection
