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
            <a href="{{ route('catalog') }}" class="px-8 py-3 bg-linear-to-r from-sky-500 to-indigo-600 text-white rounded-2xl font-bold hover:shadow-lg transition-all">Mulai Belanja</a>
        </div>
    @else
        <div class="grid md:grid-cols-3 gap-6">

            {{-- Cart Items --}}
            <div class="md:col-span-2 space-y-3" id="cart-items-container">
                @foreach($cart as $id => $item)
                <div class="bg-white rounded-2xl border border-slate-100 p-4 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow cart-item"
                     id="cart-item-{{ $id }}"
                     data-id="{{ $id }}"
                     data-price="{{ $item['price'] }}"
                     data-type="{{ $item['type'] }}">
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
                        {{-- Quantity controls --}}
                        <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                            <button type="button" data-product-id="{{ $id }}" data-delta="-1"
                                class="qty-btn px-2.5 py-1.5 hover:bg-slate-100 text-slate-600 font-bold transition-colors select-none"
                                id="btn-minus-{{ $id }}">−</button>
                            <span class="w-10 text-center text-sm py-1.5 border-x border-slate-200 font-semibold text-slate-700 qty-display"
                                id="qty-{{ $id }}">{{ $item['quantity'] }}</span>
                            <button type="button" data-product-id="{{ $id }}" data-delta="1"
                                class="qty-btn px-2.5 py-1.5 hover:bg-slate-100 text-slate-600 font-bold transition-colors select-none"
                                id="btn-plus-{{ $id }}">+</button>
                        </div>

                        <p class="text-sm font-bold text-slate-700 w-24 text-right item-subtotal" id="subtotal-{{ $id }}">
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
                            <span id="summary-count">{{ count($cart) }} item</span>
                            <span id="summary-subtotal">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        @php $hasPhysical = collect($cart)->contains(fn($i) => $i['type'] === 'physical'); @endphp
                        <div class="flex justify-between text-slate-500 {{ $hasPhysical ? '' : 'hidden' }}" id="shipping-row">
                            <span>Ongkir (estimasi)</span>
                            <span>Rp 15.000</span>
                        </div>
                        <div class="border-t border-slate-100 pt-2 flex justify-between font-bold text-slate-800">
                            <span>Total</span>
                            <span class="text-sky-600" id="summary-total">Rp {{ number_format($total + ($hasPhysical ? 15000 : 0), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customer.checkout') }}"
                       class="mt-5 block text-center py-3.5 bg-linear-to-r from-sky-500 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-xl hover:shadow-sky-200 transition-all hover:-translate-y-0.5">
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
const CSRF = '{{ csrf_token() }}';
let updating = {};

function formatRupiah(num) {
    return 'Rp ' + num.toLocaleString('id-ID');
}

function recalcSummary() {
    let total = 0;
    let hasPhysical = false;
    document.querySelectorAll('.cart-item').forEach(el => {
        const price = parseFloat(el.dataset.price);
        const qty   = parseInt(document.getElementById('qty-' + el.dataset.id).textContent);
        total += price * qty;
        if (el.dataset.type === 'physical') hasPhysical = true;
    });

    const shipping = hasPhysical ? 15000 : 0;
    document.getElementById('summary-subtotal').textContent = formatRupiah(total);
    document.getElementById('summary-total').textContent    = formatRupiah(total + shipping);

    const shippingRow = document.getElementById('shipping-row');
    if (shippingRow) {
        shippingRow.classList.toggle('hidden', !hasPhysical);
    }
}

async function changeQty(productId, delta) {
    if (updating[productId]) return;

    const qtyEl   = document.getElementById('qty-' + productId);
    const current = parseInt(qtyEl.textContent);
    const newQty  = current + delta;

    if (newQty < 1) return;

    // Optimistic UI
    updating[productId] = true;
    qtyEl.textContent = newQty;

    const itemEl = document.getElementById('cart-item-' + productId);
    const price  = parseFloat(itemEl.dataset.price);
    document.getElementById('subtotal-' + productId).textContent = formatRupiah(price * newQty);
    recalcSummary();

    // Disable buttons
    const btnMinus = document.getElementById('btn-minus-' + productId);
    const btnPlus  = document.getElementById('btn-plus-' + productId);
    btnMinus.disabled = true;
    btnPlus.disabled  = true;
    btnMinus.classList.add('opacity-40');
    btnPlus.classList.add('opacity-40');

    try {
        const res = await fetch(`/cart/update/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'text/html',
            },
            body: '_method=PATCH&quantity=' + newQty,
            redirect: 'follow',
        });

        if (!res.ok) throw new Error('Update gagal');
    } catch (e) {
        // Revert on error
        qtyEl.textContent = current;
        document.getElementById('subtotal-' + productId).textContent = formatRupiah(price * current);
        recalcSummary();

        const toast = document.createElement('div');
        toast.className = 'fixed top-5 right-5 z-50 px-5 py-3 bg-red-500 text-white text-sm font-semibold rounded-2xl shadow-2xl';
        toast.textContent = 'Gagal memperbarui jumlah. Coba lagi.';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    } finally {
        updating[productId] = false;
        btnMinus.disabled = false;
        btnPlus.disabled  = false;
        btnMinus.classList.remove('opacity-40');
        btnPlus.classList.remove('opacity-40');
    }
}

// Wire up qty buttons via event delegation (avoids inline onclick with Blade syntax)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.qty-btn');
    if (!btn) return;
    const productId = parseInt(btn.dataset.productId);
    const delta     = parseInt(btn.dataset.delta);
    if (productId && delta) changeQty(productId, delta);
});
</script>
@endpush
@endsection
