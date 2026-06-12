<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Public landing page for service info.
     */
    public function landing()
    {
        $serviceOrders = null;
        if (Auth::check()) {
            $serviceOrders = ServiceOrder::where('user_id', Auth::id())
                ->latest()
                ->take(3)
                ->get();
        }

        return view('customer.service.landing', compact('serviceOrders'));
    }

    /**
     * Authenticated user: list their service orders.
     */
    public function index()
    {
        $serviceOrders = ServiceOrder::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.service.index', compact('serviceOrders'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('customer.service.create');
    }

    /**
     * Store a new service order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_brand'       => 'required|string|max:50',
            'device_model'       => 'required|string|max:100',
            'device_color'       => 'nullable|string|max:30',
            'device_imei'        => 'nullable|string|max:20',
            'damage_type'        => 'required|string|in:lcd,battery,charging_port,software,water_damage,speaker,camera,button,other',
            'damage_description' => 'required|string|max:1000',
            'device_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'contact_name'       => 'required|string|max:100',
            'contact_phone'      => 'required|string|max:20',
            'contact_address'    => 'nullable|string|max:500',
        ]);

        $imagePath = null;
        if ($request->hasFile('device_image')) {
            $imagePath = $request->file('device_image')->store('service-devices', 'public');
        }

        $serviceOrder = ServiceOrder::create([
            'service_number'     => ServiceOrder::generateServiceNumber(),
            'user_id'            => Auth::id(),
            'device_brand'       => $request->device_brand,
            'device_model'       => $request->device_model,
            'device_color'       => $request->device_color,
            'device_imei'        => $request->device_imei,
            'damage_type'        => $request->damage_type,
            'damage_description' => $request->damage_description,
            'device_image'       => $imagePath,
            'contact_name'       => $request->contact_name,
            'contact_phone'      => $request->contact_phone,
            'contact_address'    => $request->contact_address,
            'status'             => 'pending',
        ]);

        // Create Admin Notification
        try {
            \App\Models\AdminNotification::create([
                'title' => 'Permintaan Servis 🔧',
                'message' => 'Permintaan servis ' . $serviceOrder->service_number . ' (' . $serviceOrder->device_brand . ' ' . $serviceOrder->device_model . ') dari ' . $serviceOrder->contact_name,
                'type' => 'service',
                'link' => route('admin.services.show', $serviceOrder->id, false),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to create Admin Notification for service order: ' . $e->getMessage());
        }

        return redirect()
            ->route('customer.service.show', $serviceOrder->id)
            ->with('success', 'Permintaan servis berhasil diajukan! Nomor servis: ' . $serviceOrder->service_number);
    }

    /**
     * Show service order detail with tracking.
     */
    public function show(ServiceOrder $serviceOrder)
    {
        abort_if($serviceOrder->user_id !== Auth::id(), 403);

        // Sync payment status with Midtrans in real-time
        if ($serviceOrder->payment_method === 'midtrans' && $serviceOrder->payment_status !== 'paid') {
            $serviceOrder->syncWithMidtrans();
        }

        return view('customer.service.show', compact('serviceOrder'));
    }

    /**
     * Customer approves the estimated cost.
     */
    public function approve(ServiceOrder $serviceOrder)
    {
        abort_if($serviceOrder->user_id !== Auth::id(), 403);
        abort_if($serviceOrder->status !== 'waiting_approval', 400);

        $serviceOrder->update(['status' => 'repairing']);

        return back()->with('success', 'Estimasi biaya disetujui. Proses perbaikan akan segera dimulai.');
    }

    /**
     * Customer cancels the service order.
     */
    public function cancel(ServiceOrder $serviceOrder)
    {
        abort_if($serviceOrder->user_id !== Auth::id(), 403);
        abort_if(in_array($serviceOrder->status, ['completed', 'picked_up', 'cancelled']), 400);

        $serviceOrder->update(['status' => 'cancelled']);

        return back()->with('success', 'Permintaan servis telah dibatalkan.');
    }

    /**
     * Customer chooses Cash payment.
     */
    public function payCash(ServiceOrder $serviceOrder)
    {
        abort_if($serviceOrder->user_id !== Auth::id(), 403);
        abort_if($serviceOrder->status !== 'completed', 400);
        abort_if($serviceOrder->payment_status === 'paid', 400);

        $serviceOrder->update([
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ]);

        return back()->with('success', 'Metode pembayaran Tunai dipilih. Silakan lakukan pembayaran langsung di toko saat pengambilan.');
    }

    /**
     * Customer uploads manual bank transfer proof.
     */
    public function payTransfer(Request $request, ServiceOrder $serviceOrder)
    {
        abort_if($serviceOrder->user_id !== Auth::id(), 403);
        abort_if($serviceOrder->status !== 'completed', 400);
        abort_if($serviceOrder->payment_status === 'paid', 400);

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $proofPath = $request->file('payment_proof')->store('service-payment-proofs', 'public');

        $serviceOrder->update([
            'payment_method' => 'transfer',
            'payment_status' => 'pending',
            'payment_proof' => $proofPath,
        ]);

        return back()->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.');
    }

    /**
     * Customer initiates Midtrans online payment.
     */
    public function payMidtrans(ServiceOrder $serviceOrder)
    {
        abort_if($serviceOrder->user_id !== Auth::id(), 403);
        abort_if($serviceOrder->status !== 'completed', 400);
        abort_if($serviceOrder->payment_status === 'paid', 400);

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id' => $serviceOrder->service_number,
                    'gross_amount' => (int) $serviceOrder->final_cost,
                ],
                'customer_details' => [
                    'first_name' => $serviceOrder->contact_name,
                    'email' => $serviceOrder->user->email,
                    'phone' => $serviceOrder->contact_phone,
                ],
                'callbacks' => [
                    'finish' => route('customer.service.show', $serviceOrder->id),
                    'unfinish' => route('customer.service.show', $serviceOrder->id),
                    'error' => route('customer.service.show', $serviceOrder->id),
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $serviceOrder->update([
                'payment_method' => 'midtrans',
                'snap_token' => $snapToken,
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Midtrans Service Snap Token failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke gateway pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }
}
