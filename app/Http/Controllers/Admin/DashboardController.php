<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Revenue metrics
        $totalRevenue = Transaction::where('transactions.status', 'verified')
            ->join('orders', 'transactions.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'processing', 'shipped', 'completed'])
            ->sum('transactions.amount');

        $monthlyRevenue = Transaction::where('status', 'verified')
            ->whereMonth('verified_at', now()->month)
            ->whereYear('verified_at', now()->year)
            ->sum('amount');

        $todayRevenue = Transaction::where('status', 'verified')
            ->whereDate('verified_at', today())
            ->sum('amount');

        // Order stats
        $totalOrders    = Order::count();
        $pendingOrders  = Order::whereIn('status', ['awaiting_payment', 'payment_uploaded'])->count();
        $processOrders  = Order::whereIn('status', ['paid', 'processing'])->count();
        $completedOrders = Order::where('status', 'completed')->count();

        // Revenue chart (last 7 days)
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = now()->subDays($i);
            $label = $date->format('d M');
            $rev   = Transaction::where('status', 'verified')
                ->whereDate('verified_at', $date->toDateString())
                ->sum('amount');
            $revenueChart[] = ['date' => $label, 'revenue' => (float) $rev];
        }

        // Revenue chart (last 6 months)
        $monthlyChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $rev  = Transaction::where('status', 'verified')
                ->whereMonth('verified_at', $date->month)
                ->whereYear('verified_at', $date->year)
                ->sum('amount');
            $monthlyChart[] = ['month' => $date->format('M Y'), 'revenue' => (float) $rev];
        }

        // Other stats
        $totalProducts  = Product::count();
        $lowStockProducts = Product::where('type', 'physical')
            ->where('stock', '<=', 5)
            ->where('is_active', true)
            ->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $pendingPayments = Transaction::where('status', 'pending')->count();

        // Recent orders
        $recentOrders = Order::with(['user', 'transaction'])
            ->latest()
            ->take(8)
            ->get();

        // Top products
        $topProducts = Product::withSum('orderItems', 'quantity')
            ->orderByDesc('order_items_sum_quantity')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'monthlyRevenue',
            'todayRevenue',
            'totalOrders',
            'pendingOrders',
            'processOrders',
            'completedOrders',
            'revenueChart',
            'monthlyChart',
            'totalProducts',
            'lowStockProducts',
            'totalCustomers',
            'pendingPayments',
            'recentOrders',
            'topProducts'
        ));
    }
}
