@extends('customer.layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-black text-slate-800 mb-6">🛒 Keranjang Belanja</h1>

    @if(empty($cart))
        <div class="text-center py-20 bg-white rounded-3xl border border-slate-100">
            <div class="text-7xl mb-4">🛍️</div>
            <h2 class="text-xl font-bold text-slate-700 mb-2">Keranjang Kosong</h2>
            <p class="text-slate-500 mb-6">Belum ada produk di keranjang Anda.</p>
            <a href="{{ route('catalog') }}" class="px-8 py-3 bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-2xl font-bold hover:shadow-lg transition-all">Mulai Belanja</a>
        </div>
    @else
        <div class="grid md:grid-cols-3 gap-6">

            {{-- Cart Items --}}
            <div class="md:col-span-2 space-y-3">
                @foreach($cart as $id => $item)
                <div class="bg-white rounded-2xl border border-slate-100 p-4 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-16 h-16 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                        @if($item['image'])
                            <img src="{{ asset('storage/'.$item['image']) }}" class="w-full h-full object-contain rounded-xl">
                        @else
                            <span class="text-2xl">📦</span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 text-sm truncate">{{ $item['name'] }}</p>
                        <p class="text-sky-600 font-bold text-sm mt-0.5">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        <span class="text-[10px] px-2 py-0.5 rounded-full {{ $item['type'] === 'digital' ? 'bg-sky-100 text-sky-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $item['type'] === 'digital' ? '⚡ Digital' : '📦 Fisik' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Update quantity --}}
                        <form action="{{ route('customer.cart.update', $id) }}" method="POST" class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                            @csrf @method('PATCH')
                            <button type="button" onclick="updateQty(this, -1)" class="px-2 py-1 hover:bg-slate-100 text-slate-600">−</button>
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                onchange="this.form.submit()"
                                class="w-10 text-center text-sm border-x border-slate-200 py-1 focus:outline-none">
                            <button type="button" onclick="updateQty(this, 1)" class="px-2 py-1 hover:bg-slate-100 text-slate-600">+</button>
                        </form>

                        <p class="text-sm font-bold text-slate-700 w-24 text-right">
                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                        </p>

                        {{-- Remove --}}
                        <form action="{{ route('customer.cart.remove', $id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach

                <div class="flex justify-end mt-2">
                    <form action="{{ route('customer.cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors">✕ Kosongkan Keranjang</button>
                    </form>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm sticky top-20">
                    <h3 class="font-bold text-slate-700 mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-500">
                            <span>{{ count($cart) }} item</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        @php $hasPhysical = collect($cart)->contains(fn($i) => $i['type'] === 'physical'); @endphp
                        @if($hasPhysical)
                            <div class="flex justify-between text-slate-500">
                                <span>Ongkir (estimasi)</span>
                                <span>Rp 15.000</span>
                            </div>
                        @endif
                        <div class="border-t border-slate-100 pt-2 flex justify-between font-bold text-slate-800">
                            <span>Total</span>
                            <span class="text-sky-600">Rp {{ number_format($total + ($hasPhysical ? 15000 : 0), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customer.checkout') }}"
                       class="mt-5 block text-center py-3.5 bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-xl hover:shadow-sky-200 transition-all hover:-translate-y-0.5">
                        Lanjut Checkout →
                    </a>

                    <a href="{{ route('catalog') }}" class="mt-3 block text-center text-sm text-slate-400 hover:text-sky-600 transition-colors">← Lanjut Belanja</a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function updateQty(btn, delta) {
    const input = btn.parentElement.querySelector('input');
    const newVal = Math.max(1, parseInt(input.value) + delta);
    input.value = newVal;
    btn.closest('form').submit();
}
</script>
@endpush
@endsection
