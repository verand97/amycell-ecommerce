@extends('customer.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-black text-slate-800 mb-6">Checkout</h1>

    <form action="{{ route('customer.checkout.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Left: Shipping Info --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Shipping --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                    <h2 class="font-bold text-slate-700 text-lg mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        Informasi Penerima
                    </h2>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Nama Penerima *</label>
                            <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name) }}" required
                                @class([
                                    'w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all',
                                    'border-red-400' => $errors->has('shipping_name'),
                                    'border-slate-200' => !$errors->has('shipping_name'),
                                ])>
                            @error('shipping_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Nomor Telepon *</label>
                            <input type="tel" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone) }}" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all">
                        </div>
                    </div>

                    @if($hasPhysical)
                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Alamat Lengkap *</label>
                            <textarea name="shipping_address" rows="3" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all resize-none">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Kota *</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Kode Pos *</label>
                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all">
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Catatan (opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Catatan khusus untuk pesanan..."
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all resize-none"></textarea>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                    <h2 class="font-bold text-slate-700 text-lg mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        Metode Pembayaran
                    </h2>
                    <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                        <p class="font-semibold text-sky-800 mb-2">🏦 Transfer Bank</p>
                        <div class="space-y-2 text-sm text-sky-700">
                            <p><strong>BCA:</strong> 1234567890 (Toko Amycell)</p>
                            <p><strong>Mandiri:</strong> 0987654321 (Toko Amycell)</p>
                            <p><strong>BNI:</strong> 1122334455 (Toko Amycell)</p>
                        </div>
                        <p class="text-xs text-sky-600 mt-3 italic">Setelah checkout, unggah bukti transfer di halaman pesanan Anda.</p>
                    </div>
                </div>
            </div>

            {{-- Right: Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm sticky top-20">
                    <h3 class="font-bold text-slate-700 mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                        @foreach($cart as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center shrink-0 text-lg">
                                {{ $item['type'] === 'digital' ? '⚡' : '📦' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-700 truncate">{{ $item['name'] }}</p>
                                <p class="text-xs text-slate-400">× {{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-xs font-bold text-slate-700">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($hasPhysical)
                        <div class="flex justify-between text-slate-500">
                            <span>Ongkir</span>
                            <span>Rp 15.000</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-slate-800 text-base pt-2 border-t border-slate-100">
                            <span>Total</span>
                            <span class="text-sky-600">Rp {{ number_format($subtotal + ($hasPhysical ? 15000 : 0), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-5 w-full py-3.5 bg-linear-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-xl hover:shadow-sky-200 transition-all hover:-translate-y-0.5 text-sm">
                        ✅ Buat Pesanan
                    </button>

                    <p class="text-xs text-slate-400 text-center mt-3">🔒 Transaksi aman & terenkripsi</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
