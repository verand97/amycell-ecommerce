<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $signatureKey = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($signatureKey !== $request->signature_key) {
            Log::warning('Midtrans Webhook: Invalid signature key', [
                'received' => $request->signature_key,
                'calculated' => $signatureKey,
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderNumber = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $paymentType = $request->payment_type;

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            Log::warning('Midtrans Webhook: Order not found', ['order_number' => $orderNumber]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Find or create Transaction
            $transaction = Transaction::where('order_id', $order->id)->first();
            
            $trxData = [
                'order_id'         => $order->id,
                'transaction_code' => $request->transaction_id ?? ($transaction?->transaction_code ?? Transaction::generateCode()),
                'amount'           => $order->total_amount,
                'payment_method'   => 'midtrans',
                'bank_name'        => $paymentType,
                'account_name'     => $order->user->name,
                'status'           => 'pending',
            ];

            if (isset($request->va_numbers[0]['bank'])) {
                $trxData['bank_name'] = strtoupper($request->va_numbers[0]['bank']);
            }

            if (!$transaction) {
                $transaction = Transaction::create($trxData);
            } else {
                $transaction->update($trxData);
            }

            if ($transactionStatus == 'capture') {
                if ($request->fraud_status == 'challenge') {
                    $order->update(['status' => 'payment_uploaded']);
                    $transaction->update(['status' => 'pending']);
                } else if ($request->fraud_status == 'accept') {
                    $order->update(['status' => 'paid', 'paid_at' => now()]);
                    $transaction->update(['status' => 'verified', 'verified_at' => now()]);
                }
            } else if ($transactionStatus == 'settlement') {
                $order->update(['status' => 'paid', 'paid_at' => now()]);
                $transaction->update(['status' => 'verified', 'verified_at' => now()]);
            } else if ($transactionStatus == 'pending') {
                $order->update(['status' => 'payment_uploaded']);
                $transaction->update(['status' => 'pending']);
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->update(['status' => 'cancelled']);
                $transaction->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Pembayaran ' . $transactionStatus . ' via Midtrans'
                ]);
            }

            DB::commit();

            // Real-time broadcast
            try {
                broadcast(new OrderStatusUpdated($order));
            } catch (\Exception $e) {
                Log::warning('Broadcast OrderStatusUpdated gagal: ' . $e->getMessage());
            }

            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
