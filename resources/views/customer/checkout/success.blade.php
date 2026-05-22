@extends('customer.layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12 text-center">

    {{-- Success Icon --}}
    <div class="w-24 h-24 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-emerald-200 animate-bounce">
        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-3xl font-black text-slate-800 mb-2">Pesanan Berhasil! 🎉</h1>
    <p class="text-slate-500 mb-8">Terima kasih telah berbelanja di Toko Amycell</p>

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

    {{-- Upload Payment Proof --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6 text-left">
        <h3 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            Langkah Selanjutnya
        </h3>
        <ol class="text-sm text-amber-700 space-y-2 list-decimal list-inside">
            <li>Transfer sejumlah <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> ke rekening Amycell</li>
            <li>Foto/screenshot bukti transfer Anda</li>
            <li>Upload bukti bayar di bawah ini</li>
        </ol>

        <form action="{{ route('customer.checkout.upload-payment', $order->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div class="grid sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="text-xs font-semibold text-amber-700 block mb-1">Bank Tujuan *</label>
                    <input type="text" name="bank_name" placeholder="Contoh: BCA" required
                        class="w-full px-3 py-2 text-sm border border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-400 outline-none bg-white">
                </div>
                <div>
                    <label class="text-xs font-semibold text-amber-700 block mb-1">Nama Pengirim *</label>
                    <input type="text" name="account_name" placeholder="Nama di rekening" required
                        class="w-full px-3 py-2 text-sm border border-amber-200 rounded-xl focus:ring-2 focus:ring-amber-400 outline-none bg-white">
                </div>
            </div>
            <div class="mb-3">
                <label class="text-xs font-semibold text-amber-700 block mb-1">Bukti Transfer *</label>
                <input type="file" name="proof_image" accept="image/*" required
                    class="w-full px-3 py-2 text-sm border border-amber-200 rounded-xl bg-white focus:ring-2 focus:ring-amber-400 outline-none">
            </div>
            <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition-colors text-sm">
                📤 Upload Bukti Bayar
            </button>
        </form>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('customer.orders.show', $order->id) }}" class="px-6 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-lg transition-all">
            Pantau Status Pesanan
        </a>
        <a href="{{ route('catalog') }}" class="px-6 py-3 bg-slate-100 text-slate-600 font-semibold rounded-2xl hover:bg-slate-200 transition-all">
            Lanjut Belanja
        </a>
    </div>
</div>
@endsection
