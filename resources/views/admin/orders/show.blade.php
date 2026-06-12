@extends('admin.layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number)
@section('page-title', 'Detail Pesanan')

@section('content')
<div class="grid lg:grid-cols-3 gap-4">

    {{-- Order Details --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="font-mono text-lg font-black text-white">{{ $order->order_number }}</p>
                    <p class="text-xs text-slate-400">{{ $order->created_at->format('d F Y, H:i') }}</p>
                    @if($order->fifo_position)
                        <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-sky-500/10 text-sky-400 text-xs font-bold rounded-xl border border-sky-500/20">
                            ⏳ Posisi Antrean FIFO: #{{ $order->fifo_position }}
                        </div>
                    @endif
                </div>
                @php
                    $statusClass = match($order->status_color) {
                        'yellow' => 'bg-yellow-500/20 text-yellow-400',
                        'blue' => 'bg-blue-500/20 text-blue-400',
                        'green' => 'bg-emerald-500/20 text-emerald-400',
                        'red' => 'bg-red-500/20 text-red-400',
                        'indigo' => 'bg-indigo-500/20 text-indigo-400',
                        'purple' => 'bg-purple-500/20 text-purple-400',
                        default => 'bg-slate-700 text-slate-300',
                    };
                @endphp
                <span class="px-4 py-1.5 text-sm font-bold rounded-full {{ $statusClass }}">
                    {{ $order->status_label }}
                </span>
            </div>

            {{-- Items --}}
            <div class="space-y-2">
                @foreach($order->items as $item)
                <div class="flex items-center gap-3 py-2 border-b border-slate-800 last:border-0">
                    <div class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-xl">📦</div>
                    <div class="flex-1">
                        <p class="text-sm text-slate-200 font-medium">{{ $item->product_name }}</p>
                        <p class="text-xs text-slate-500">Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                    </div>
                    <p class="font-bold text-sky-400 text-sm">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-slate-800 space-y-2">
                <div class="flex justify-between text-sm text-slate-400"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                @if($order->shipping_cost > 0)
                    <div class="flex justify-between text-sm text-slate-400"><span>Ongkir</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
                @endif
                <div class="flex justify-between font-bold text-white text-base"><span>Total</span><span class="text-sky-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
            </div>
        </div>

        {{-- Payment Info --}}
        @if($order->payment_proof_url || $order->transaction)
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                💳 Detail Pembayaran
            </h3>

            @if($order->payment_proof_url)
                <div class="mb-4">
                    <p class="text-xs text-slate-400 mb-2">Bukti Pembayaran (Manual Transfer):</p>
                    <a href="{{ $order->payment_proof_url }}" target="_blank">
                        <img src="{{ $order->payment_proof_url }}" class="max-h-48 rounded-xl mx-auto object-contain hover:scale-105 transition-transform" alt="Bukti Bayar">
                    </a>
                </div>
            @endif

            @if($order->transaction)
                <div class="pt-3 {{ $order->payment_proof_url ? 'border-t border-slate-800' : '' }} space-y-3.5 text-xs">
                    <div class="flex justify-between border-b border-slate-800/50 pb-2">
                        <span class="text-slate-500">Kode Transaksi:</span>
                        <span class="font-mono text-slate-200 font-semibold">{{ $order->transaction->transaction_code }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-800/50 pb-2">
                        <span class="text-slate-500">Tipe Pembayaran:</span>
                        <span class="text-slate-200 font-semibold uppercase">{{ str_replace('_', ' ', $order->transaction->payment_type) }}</span>
                    </div>
                    @if($order->transaction->bank_name)
                    <div class="flex justify-between border-b border-slate-800/50 pb-2">
                        <span class="text-slate-500">Bank Pengirim:</span>
                        <span class="text-slate-200 font-semibold uppercase">{{ $order->transaction->bank_name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status Pembayaran:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $order->transaction->status === 'verified' ? 'bg-emerald-500/20 text-emerald-400' : ($order->transaction->status === 'rejected' ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400') }}">
                            {{ $order->transaction->status_label }}
                        </span>
                    </div>

                    @if($order->transaction->rejection_reason)
                        <p class="text-xs text-red-400 mb-3 bg-red-500/10 p-2.5 rounded-xl border border-red-500/20">
                            <strong>Alasan Penolakan:</strong> {{ $order->transaction->rejection_reason }}
                        </p>
                    @endif

                    {{-- Actions for manual payments verification only --}}
                    @if($order->payment_proof_url)
                        <div class="flex gap-2 justify-end pt-2">
                            @if($order->transaction->status !== 'verified')
                                <form action="{{ route('admin.transactions.verify', $order->transaction->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                        {{ $order->transaction->status === 'rejected' ? '✅ Verifikasi Ulang' : '✅ Verifikasi' }}
                                    </button>
                                </form>
                            @endif

                            @if($order->transaction->status !== 'rejected')
                                <button type="button" onclick="showRejectModal('{{ $order->transaction->id }}')" class="px-3.5 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-bold rounded-xl border border-red-500/20 transition-colors cursor-pointer">
                                    {{ $order->transaction->status === 'verified' ? '❌ Batalkan & Tolak' : '❌ Tolak' }}
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        {{-- Customer Info --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <h3 class="text-sm font-bold text-slate-300 mb-3">Informasi Pelanggan</h3>
            <div class="space-y-2 text-xs">
                <p class="text-slate-400"><span class="text-slate-500">Nama:</span> {{ $order->user->name }}</p>
                <p class="text-slate-400"><span class="text-slate-500">Email:</span> {{ $order->user->email }}</p>
                <p class="text-slate-400"><span class="text-slate-500">HP:</span> {{ $order->user->phone ?? '-' }}</p>
            </div>
        </div>

        {{-- Shipping Info --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <h3 class="text-sm font-bold text-slate-300 mb-3">Informasi Pengiriman</h3>
            <div class="space-y-2 text-xs text-slate-400">
                <p><span class="text-slate-500">Penerima:</span> {{ $order->shipping_name }}</p>
                <p><span class="text-slate-500">HP:</span> {{ $order->shipping_phone }}</p>
                <p><span class="text-slate-500">Alamat:</span> {{ $order->shipping_address }}, {{ $order->shipping_city }} {{ $order->shipping_postal_code }}</p>
            </div>
        </div>

        {{-- Update Status --}}
        @if(in_array($order->status, ['awaiting_payment', 'payment_uploaded', 'paid', 'processing', 'shipped']))
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
            <h3 class="text-sm font-bold text-slate-300 mb-3">Perbarui Status</h3>
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                @csrf
                <select name="status" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none mb-2">
                    @if(in_array($order->status, ['awaiting_payment', 'payment_uploaded']))
                        <option value="awaiting_payment" {{ $order->status === 'awaiting_payment' ? 'selected' : '' }}>⏳ Menunggu Pembayaran</option>
                        <option value="payment_uploaded" {{ $order->status === 'payment_uploaded' ? 'selected' : '' }}>📤 Bukti Bayar Dikirim</option>
                        <option value="paid">✅ Konfirmasi Pembayaran</option>
                    @endif
                    @if(in_array($order->status, ['paid', 'processing', 'shipped']))
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>⚙️ Diproses</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>🚚 Dikirim</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                    @endif
                    <option value="cancelled">❌ Batalkan</option>
                </select>
                <input type="text" name="admin_notes" placeholder="Catatan admin (opsional)" value="{{ $order->admin_notes }}"
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-slate-200 focus:ring-2 focus:ring-sky-500 outline-none mb-2">
                <button type="submit" class="w-full py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold rounded-xl transition-colors">
                    Update Status (Real-time)
                </button>
            </form>
            <p class="text-[10px] text-slate-500 mt-2 text-center">⚡ Perubahan dikirim real-time ke pelanggan</p>
        </div>
        @endif
    </div>
</div>

<div id="reject-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-md">
        <h3 class="font-bold text-white mb-4">❌ Tolak Transaksi</h3>
        <form id="reject-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-400 mb-2">Alasan Penolakan *</label>
                <textarea name="rejection_reason" rows="3" required placeholder="Jelaskan alasan penolakan..."
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm text-slate-200 focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl text-sm transition-colors">Tolak Transaksi</button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-2.5 bg-slate-800 text-slate-300 rounded-xl text-sm hover:bg-slate-700 transition-colors">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showRejectModal(transactionId) {
    document.getElementById('reject-form').action = `/admin/transactions/${transactionId}/reject`;
    const modal = document.getElementById('reject-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
@endsection
