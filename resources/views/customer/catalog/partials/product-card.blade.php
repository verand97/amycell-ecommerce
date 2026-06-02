@php
$dark = $dark ?? false;
$effectivePrice = $product->effective_price;
$hasDiscount = !is_null($product->sale_price);
$discountPct = $product->discount_percentage;
@endphp
<a href="{{ route('catalog.show', $product->slug) }}"
   class="group relative flex flex-col rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl
          {{ $dark ? 'bg-sky-950/40 border border-sky-850 hover:border-sky-500' : 'bg-white border border-slate-100 hover:border-sky-300 hover:shadow-sky-500/5' }}">

    {{-- Badge --}}
    @if($hasDiscount)
        <div class="absolute top-2 left-2 z-10 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">-{{ $discountPct }}%</div>
    @endif
    @if($product->is_featured)
        <div class="absolute top-2 right-2 z-10 bg-amber-400 text-amber-900 text-[10px] font-bold px-2 py-0.5 rounded-full">⭐ Unggulan</div>
    @endif

    {{-- Image --}}
    <div class="aspect-square {{ $dark ? 'bg-sky-950/60' : 'bg-linear-to-br from-slate-50 to-slate-100' }} flex items-center justify-center p-4 relative overflow-hidden">
        @if($product->image)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-300">
        @else
            <div class="text-5xl opacity-60 group-hover:scale-110 transition-transform duration-300">
                {{ $product->category->icon ?? '📦' }}
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="p-3 flex flex-col flex-1">
        <span class="text-[10px] font-semibold {{ $dark ? 'text-sky-350' : 'text-sky-600' }} uppercase tracking-wide mb-1">{{ $product->category->name }}</span>
        <h3 class="text-sm font-semibold {{ $dark ? 'text-white' : 'text-slate-800' }} leading-snug line-clamp-2 flex-1">{{ $product->name }}</h3>

        <div class="mt-2">
            @if($hasDiscount)
                <p class="text-xs text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            @endif
            <p class="text-base font-black {{ $dark ? 'text-sky-350' : 'text-sky-600' }}">
                Rp {{ number_format($effectivePrice, 0, ',', '.') }}
            </p>
        </div>

        <div class="flex items-center justify-between mt-2">
            <span class="text-[10px] {{ $dark ? 'text-sky-100/50' : 'text-slate-400' }}">
                {{ $product->type === 'digital' ? '⚡ Digital' : '📦 Fisik' }}
                @if($product->type === 'physical')
                    · Stok {{ $product->stock }}
                @endif
            </span>
            <span class="text-[10px] {{ $dark ? 'text-sky-100/50' : 'text-slate-400' }}">{{ number_format($product->sold_count) }} terjual</span>
        </div>

        <form action="{{ route('customer.cart.add', $product->id) }}" method="POST" class="mt-3" onclick="event.stopPropagation(); event.preventDefault(); this.submit();">
            @csrf
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                    class="w-full py-2 rounded-xl text-xs font-bold transition-all
                           {{ !$product->isInStock() ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : ($dark ? 'bg-sky-500 hover:bg-sky-400 text-white shadow-md' : 'bg-sky-500 hover:bg-sky-600 text-white shadow-md') }}"
                    {{ !$product->isInStock() ? 'disabled' : '' }}>
                {{ $product->isInStock() ? '+ Keranjang' : 'Habis' }}
            </button>
        </form>
    </div>
</a>
