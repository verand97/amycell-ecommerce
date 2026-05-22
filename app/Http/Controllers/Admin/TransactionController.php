<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['order.user'])
            ->orderBy('created_at', 'asc'); // FIFO for transaction review

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $transactions  = $query->paginate(15)->withQueryString();
        $pendingCount  = Transaction::where('status', 'pending')->count();
        $verifiedCount = Transaction::where('status', 'verified')->count();

        return view('admin.transactions.index', compact('transactions', 'pendingCount', 'verifiedCount'));
    }

    public function verify(Request $request, Transaction $transaction)
    {
        $request->validate(['notes' => 'nullable|string|max:500']);

        abort_if($transaction->status !== 'pending', 422);

        DB::beginTransaction();
        try {
            $transaction->update([
                'status'      => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'notes'       => $request->notes,
            ]);

            $order = $transaction->order;
            $order->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Verifikasi gagal: ' . $e->getMessage());
        }

        // Broadcast di luar transaksi DB — gagal broadcast tidak batalkan verifikasi
        try {
            broadcast(new OrderStatusUpdated($order));
        } catch (\Exception $e) {
            // Log tapi jangan gagalkan response
            logger()->warning('Broadcast OrderStatusUpdated gagal: ' . $e->getMessage());
        }

        return back()->with('success', "Transaksi #{$transaction->transaction_code} berhasil diverifikasi. Status pesanan diperbarui ke 'Dibayar'.");
    }

    public function reject(Request $request, Transaction $transaction)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        abort_if($transaction->status !== 'pending', 422);

        DB::beginTransaction();
        try {
            $transaction->update([
                'status'           => 'rejected',
                'verified_by'      => auth()->id(),
                'verified_at'      => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            $order = $transaction->order;
            $order->update(['status' => 'awaiting_payment']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Penolakan gagal: ' . $e->getMessage());
        }

        try {
            broadcast(new OrderStatusUpdated($order));
        } catch (\Exception $e) {
            logger()->warning('Broadcast OrderStatusUpdated gagal: ' . $e->getMessage());
        }

        return back()->with('success', 'Transaksi ditolak. Customer diberitahu untuk mengirim ulang bukti bayar.');
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status'      => 'required|in:processing,shipped,completed,cancelled',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $updates = [
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->status === 'shipped') $updates['shipped_at'] = now();
        if ($request->status === 'completed') $updates['completed_at'] = now();

        $order->update($updates);

        // Real-time broadcast — gagal broadcast tidak batalkan update status
        try {
            broadcast(new OrderStatusUpdated($order));
        } catch (\Exception $e) {
            logger()->warning('Broadcast OrderStatusUpdated gagal: ' . $e->getMessage());
        }

        return back()->with('success', "Status pesanan diperbarui ke: {$order->fresh()->status_label}");
    }
}
