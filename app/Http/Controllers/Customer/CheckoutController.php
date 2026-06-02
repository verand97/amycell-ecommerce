<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.cart')->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $hasPhysical = collect($cart)->contains(fn($i) => $i['type'] === 'physical');

        return view('customer.checkout.index', compact('cart', 'subtotal', 'hasPhysical'));
    }

    public function store(Request $request)
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.cart')->with('error', 'Keranjang kosong.');
        }

        $hasPhysical = collect($cart)->contains(fn($i) => $i['type'] === 'physical');

        $rules = [
            'shipping_name'        => 'required|string|max:100',
            'shipping_phone'       => 'required|string|max:20',
            'shipping_address'     => $hasPhysical ? 'required|string' : 'nullable|string',
            'shipping_city'        => $hasPhysical ? 'required|string|max:100' : 'nullable|string',
            'shipping_postal_code' => $hasPhysical ? 'required|string|max:10' : 'nullable|string',
            'notes'                => 'nullable|string|max:500',
        ];

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $subtotal     = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
            $shippingCost = $hasPhysical ? 15000 : 0;
            $total        = $subtotal + $shippingCost;

            $order = Order::create([
                'order_number'         => Order::generateOrderNumber(),
                'user_id'              => auth()->id(),
                'subtotal'             => $subtotal,
                'shipping_cost'        => $shippingCost,
                'total_amount'         => $total,
                'status'               => 'awaiting_payment',
                'shipping_name'        => $request->shipping_name,
                'shipping_phone'       => $request->shipping_phone,
                'shipping_address'     => $request->shipping_address ?? '-',
                'shipping_city'        => $request->shipping_city ?? '-',
                'shipping_postal_code' => $request->shipping_postal_code ?? '00000',
                'payment_method'       => 'midtrans',
                'notes'                => $request->notes,
            ]);

            foreach ($cart as $item) {
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['product_id'],
                    'product_name'  => $item['name'],
                    'product_image' => $item['image'],
                    'quantity'      => $item['quantity'],
                    'price'         => $item['price'],
                    'subtotal'      => $item['price'] * $item['quantity'],
                ]);

                // Decrease physical stock
                if ($product && $product->isPhysical()) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Generate Midtrans Snap Token
            try {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
                \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_number,
                        'gross_amount' => (int) $order->total_amount,
                    ],
                    'customer_details' => [
                        'first_name' => $order->user->name,
                        'email' => $order->user->email,
                        'phone' => $order->shipping_phone ?? $order->user->phone ?? '',
                    ],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Midtrans Snap Token generation failed: ' . $e->getMessage());
            }

            Session::forget('cart');
            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $order->snap_token,
                    'redirect_url' => route('customer.checkout.success', $order->order_number),
                ]);
            }

            return redirect()->route('customer.checkout.success', $order->order_number)
                ->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->with('items')
            ->firstOrFail();

        if (empty($order->snap_token) && $order->status === 'awaiting_payment') {
            try {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
                \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_number,
                        'gross_amount' => (int) $order->total_amount,
                    ],
                    'customer_details' => [
                        'first_name' => $order->user->name,
                        'email' => $order->user->email,
                        'phone' => $order->shipping_phone ?? $order->user->phone ?? '',
                    ],
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Midtrans Snap Token regeneration failed: ' . $e->getMessage());
            }
        }

        return view('customer.checkout.success', compact('order'));
    }

    public function uploadPayment(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'bank_name'   => 'required|string|max:50',
            'account_name'=> 'required|string|max:100',
        ]);

        $path = $request->file('proof_image')->store('payment-proofs', 'public');

        DB::beginTransaction();
        try {
            $order->update([
                'payment_proof' => $path,
                'status'        => 'payment_uploaded',
            ]);

            Transaction::create([
                'order_id'         => $order->id,
                'transaction_code' => Transaction::generateCode(),
                'amount'           => $order->total_amount,
                'payment_method'   => 'bank_transfer',
                'bank_name'        => $request->bank_name,
                'account_name'     => $request->account_name,
                'proof_image'      => $path,
                'status'           => 'pending',
            ]);

            DB::commit();
            return back()->with('success', 'Bukti pembayaran berhasil dikirim. Admin akan memverifikasi segera.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim bukti: ' . $e->getMessage());
        }
    }
}
