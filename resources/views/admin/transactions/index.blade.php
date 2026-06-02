@extends('admin.layouts.app')

@section('title', 'Verifikasi Transaksi')
@section('page-title', 'Verifikasi Transaksi')
@section('page-subtitle', 'Validasi pembayaran & update status pesanan real-time')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-slate-900 border border-amber-500/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-black text-amber-400">{{ $pendingCount }}</p>
        <p class="text-xs text-slate-400 mt-1">Menunggu Verifikasi</p>
    </div>
    <div class="bg-slate-900 border border-emerald-500/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-black text-emerald-400">{{ $verifiedCount }}</p>
        <p class="text-xs text-slate-400 mt-1">Terverifikasi</p>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 text-center">
        <p class="text-2xl font-black text-slate-300">{{ $transactions->total() }}</p>
        <p class="text-xs text-slate-400 mt-1">Total Transaksi</p>
    </div>
</div>

{{-- Filter --}}
<div class="flex gap-2 mb-5">
    <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ !request('status') ? 'bg-sky-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">Semua</a>
    <a href="{{ route('admin.transactions.index') }}?status=pending" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ request('status') === 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">⏳ Pending ({{ $pendingCount }})</a>
    <a href="{{ route('admin.transactions.index') }}?status=verified" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ request('status') === 'verified' ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">✅ Terverifikasi</a>
    <a href="{{ route('admin.transactions.index') }}?status=rejected" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">❌ Ditolak</a>
</div>

<div class="space-y-3">
    @forelse($transactions as $trx)
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition-all">
        <div class="flex flex-wrap items-start gap-5">

            {{-- Payment Proof --}}
            @if($trx->proof_image_url)
                <div class="shrink-0">
                    <a href="{{ $trx->proof_image_url }}" target="_blank">
                        <img src="{{ $trx->proof_image_url }}" class="w-20 h-20 object-cover rounded-xl border border-slate-700 hover:scale-105 transition-transform" alt="Bukti Bayar">
                    </a>
                    <p class="text-[10px] text-slate-500 text-center mt-1">Tap untuk besar</p>
                </div>
            @endif

            {{-- Transaction Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <span class="font-mono text-sm font-bold text-slate-200">{{ $trx->transaction_code }}</span>
                    <span class="px-2 py-1 text-[10px] font-bold rounded-full
                        {{ $trx->status === 'pending' ? 'bg-amber-500/20 text-amber-400' : ($trx->status === 'verified' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400') }}">
                        {{ $trx->status_label }}
                    </span>
                </div>

                <div class="grid sm:grid-cols-2 gap-x-6 gap-y-1.5 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 text-xs">Pelanggan:</span>
                        <span class="text-slate-300 font-medium text-xs">{{ $trx->order->user->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 text-xs">Pesanan:</span>
                        <a href="{{ route('admin.orders.show', $trx->order->id) }}" class="text-sky-400 hover:text-sky-300 text-xs font-mono">{{ $trx->order->order_number }}</a>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 text-xs">Jumlah:</span>
                        <span class="text-emerald-400 font-black text-sm">Rp {{ number_format($trx->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-slate-500 text-xs">Bank:</span>
                        <span class="text-slate-300 text-xs">{{ $trx->bank_name ?? '-' }} · {{ $trx->account_name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 sm:col-span-2">
                        <span class="text-slate-500 text-xs">Dikirim:</span>
                        <span class="text-slate-400 text-xs">{{ $trx->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col gap-2 shrink-0 justify-center">
                @if($trx->status === 'pending')
                    {{-- Verify --}}
                    <form action="{{ route('admin.transactions.verify', $trx->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Verifikasi transaksi ini? Status pesanan akan otomatis diperbarui ke DIBAYAR.')"
                            class="w-full px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition-all hover:shadow-lg cursor-pointer">
                            ✅ Verifikasi
                        </button>
                    </form>

                    {{-- Reject --}}
                    <button onclick="showRejectModal('{{ $trx->id }}')"
                        class="w-full px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-bold rounded-xl border border-red-500/20 transition-all cursor-pointer">
                        ❌ Tolak
                    </button>
                @elseif($trx->status === 'verified')
                    <div class="text-right">
                        <p class="text-xs text-emerald-400 font-semibold">✅ Terverifikasi</p>
                        <p class="text-[11px] text-slate-500 mt-1">oleh {{ $trx->verifiedBy?->name ?? '-' }}</p>
                        <p class="text-[11px] text-slate-600 mb-2">{{ $trx->verified_at?->format('d M Y H:i') }}</p>
                        <button onclick="showRejectModal('{{ $trx->id }}')"
                            class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-[10px] font-bold rounded-lg border border-red-500/20 transition-all cursor-pointer">
                            ❌ Batalkan & Tolak
                        </button>
                    </div>
                @elseif($trx->status === 'rejected')
                    <div class="text-right">
                        <p class="text-xs text-red-400 font-semibold">❌ Ditolak</p>
                        @if($trx->rejection_reason)
                            <p class="text-[10px] text-slate-400 max-w-[200px] truncate mb-2 mt-1" title="{{ $trx->rejection_reason }}">Alasan: {{ $trx->rejection_reason }}</p>
                        @endif
                        <form action="{{ route('admin.transactions.verify', $trx->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Verifikasi ulang transaksi ini? Status pesanan akan diperbarui ke DIBAYAR.')"
                                class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-bold rounded-lg transition-colors cursor-pointer">
                                ✅ Verifikasi Ulang
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12 bg-slate-900 rounded-2xl border border-slate-800">
        <div class="text-4xl mb-2">✅</div>
        <p class="text-slate-400">Tidak ada transaksi {{ request('status') === 'pending' ? 'yang menunggu verifikasi' : '' }}</p>
    </div>
    @endforelse
</div>

{{ $transactions->links() }}

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
