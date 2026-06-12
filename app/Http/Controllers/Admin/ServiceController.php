<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * List all service orders with filters.
     */
    public function index(Request $request)
    {
        $query = ServiceOrder::with('user')->oldest(); // FIFO: Oldest service requests first

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('service_number', 'like', "%{$search}%")
                  ->orWhere('device_brand', 'like', "%{$search}%")
                  ->orWhere('device_model', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $serviceOrders = $query->paginate(15);

        // Stats
        $stats = [
            'total'       => ServiceOrder::count(),
            'pending'     => ServiceOrder::where('status', 'pending')->count(),
            'in_progress' => ServiceOrder::whereIn('status', ['received', 'diagnosing', 'waiting_approval', 'repairing', 'testing'])->count(),
            'completed'   => ServiceOrder::whereIn('status', ['completed', 'picked_up'])->count(),
        ];

        return view('admin.services.index', compact('serviceOrders', 'stats'));
    }

    /**
     * Show service order detail.
     */
    public function show(ServiceOrder $serviceOrder)
    {
        $serviceOrder->load('user');

        return view('admin.services.show', compact('serviceOrder'));
    }

    /**
     * Update service order status and details.
     */
    public function updateStatus(Request $request, ServiceOrder $serviceOrder)
    {
        $request->validate([
            'status'               => 'required|string|in:pending,received,diagnosing,waiting_approval,repairing,testing,completed,picked_up,cancelled',
            'estimated_cost'       => 'nullable|numeric|min:0',
            'final_cost'           => 'nullable|numeric|min:0',
            'diagnosis_notes'      => 'nullable|string|max:2000',
            'admin_notes'          => 'nullable|string|max:2000',
            'estimated_completion' => 'nullable|date',
            'payment_status'       => 'nullable|string|in:unpaid,pending,paid',
            'payment_method'       => 'nullable|string|in:cash,transfer,midtrans',
        ]);

        $data = [
            'status' => $request->status,
        ];

        if ($request->filled('estimated_cost')) {
            $data['estimated_cost'] = $request->estimated_cost;
        }
        if ($request->filled('final_cost')) {
            $data['final_cost'] = $request->final_cost;
        }
        if ($request->filled('diagnosis_notes')) {
            $data['diagnosis_notes'] = $request->diagnosis_notes;
        }
        if ($request->filled('admin_notes')) {
            $data['admin_notes'] = $request->admin_notes;
        }
        if ($request->filled('estimated_completion')) {
            $data['estimated_completion'] = $request->estimated_completion;
        }
        if ($request->filled('payment_status')) {
            $data['payment_status'] = $request->payment_status;
            if ($request->payment_status === 'paid') {
                $data['paid_at'] = now();
            } else {
                $data['paid_at'] = null;
            }
        }
        if ($request->filled('payment_method')) {
            $data['payment_method'] = $request->payment_method;
        }

        // Set completed_at when status is completed
        if ($request->status === 'completed' && $serviceOrder->status !== 'completed') {
            $data['completed_at'] = now();
        }

        $serviceOrder->update($data);

        return back()->with('success', 'Status servis berhasil diperbarui.');
    }

    /**
     * Verify manual transfer payment.
     */
    public function verifyPayment(ServiceOrder $serviceOrder)
    {
        abort_if($serviceOrder->payment_status !== 'pending', 400);

        $serviceOrder->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
        ]);

        return back()->with('success', 'Pembayaran transfer manual berhasil diverifikasi.');
    }
}
