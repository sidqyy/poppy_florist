<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Material;
use App\Models\PromoUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $today = Carbon::today();
        
        // Orders today
        $todayOrders = Order::whereDate('created_at', $today)->count();
        
        // Online vs Offline today
        $onlineOrders = Order::whereDate('created_at', $today)->where('source', 'online')->count();
        $offlineOrders = Order::whereDate('created_at', $today)->where('source', 'offline')->count();
        
        // Revenue today (from completed payments today)
        $todayRevenue = \App\Models\Payment::whereDate('verified_at', $today)->where('status', 'verified')->sum('amount');
        
        // Processing / Pending / Ready orders
        $processingOrders = Order::where('status', 'processing')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::whereDate('completed_at', $today)->count();
        
        // Urgent orders
        $urgentOrders = Order::where('is_urgent', true)->whereNotIn('status', ['completed', 'cancelled'])->count();
        
        // Low Stock Materials (Threshold: < 10)
        $lowStockMaterials = Material::where('stock', '<', 10)->get();
        
        // Top 5 Selling Products this month
        $topProducts = \App\Models\OrderItem::select('product_name', DB::raw('count(*) as total_sales'), DB::raw('sum(subtotal) as total_revenue'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('product_name')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get();
            
        // Marketing performance (Total online vs offline revenue this month)
        $monthlyOnlineRevenue = Order::where('source', 'online')->whereMonth('created_at', Carbon::now()->month)->sum('total_amount');
        $monthlyOfflineRevenue = Order::where('source', 'offline')->whereMonth('created_at', Carbon::now()->month)->sum('total_amount');

        // Active rentals
        $activeRentals = \App\Models\OrderItem::where('is_rented', true)
            ->whereHas('order', function($q) {
                $q->whereNotIn('status', ['cancelled']);
            })->count();

        // Rentals due today
        $rentalsDueToday = \App\Models\OrderItem::where('is_rented', true)
            ->whereHas('order', function($q) {
                $q->whereNotIn('status', ['cancelled']);
            })
            ->get()
            ->filter(function($item) {
                if (!$item->order) return false;
                $dueDate = Carbon::parse($item->order->created_at)->addDays($item->rental_duration)->toDateString();
                return $dueDate === Carbon::today()->toDateString();
            })
            ->count();

        // Payment methods this month
        $paymentMethods = \App\Models\Payment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->whereMonth('verified_at', Carbon::now()->month)
            ->where('status', 'verified')
            ->groupBy('payment_method')
            ->get();

        // Materials used today
        $materialsUsedToday = \App\Models\OrderItemComponent::select('material_name', DB::raw('sum(qty) as total_qty'))
            ->whereDate('created_at', $today)
            ->groupBy('material_name')
            ->orderBy('total_qty', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'todayOrders', 'onlineOrders', 'offlineOrders', 'todayRevenue', 
            'processingOrders', 'pendingOrders', 'completedOrders', 'urgentOrders',
            'lowStockMaterials', 'topProducts', 'monthlyOnlineRevenue', 'monthlyOfflineRevenue',
            'activeRentals', 'rentalsDueToday', 'paymentMethods', 'materialsUsedToday'
        ));
    }

    public function owner()
    {
        // Owner uses the same dashboard as admin for now
        return $this->admin();
    }

    public function florist()
    {
        // Florist focuses on kitchen operations
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $urgentOrders = Order::where('is_urgent', true)->whereNotIn('status', ['completed', 'cancelled'])->count();
        
        // Ready for delivery today
        $readyOrders = Order::where('status', 'ready')->whereDate('scheduled_at', Carbon::today())->count();
        
        // Low Stock Materials (Threshold: < 10)
        $lowStockMaterials = Material::where('stock', '<', 10)->get();

        // 3 pesanan aktif terdekat yang harus dirangkai
        $nextOrders = Order::whereIn('status', ['pending', 'processing'])
            ->orderBy('is_urgent', 'desc')
            ->orderBy('scheduled_at', 'asc')
            ->take(3)
            ->get();

        return view('florist.dashboard', compact(
            'pendingOrders', 'processingOrders', 'urgentOrders', 'readyOrders', 'lowStockMaterials', 'nextOrders'
        ));
    }

    public function marketing()
    {
        $today = Carbon::today();
        
        // Online Orders today
        $todayOnlineOrders = Order::whereDate('created_at', $today)->where('source', 'online')->count();
        
        // Pending Online Orders (Need florist attention)
        $pendingOnlineOrders = Order::where('source', 'online')->where('status', 'pending')->count();
        
        // Revenue Today (Online)
        $todayOnlineRevenue = Order::where('source', 'online')
            ->whereDate('created_at', $today)
            ->whereIn('payment_status', ['paid', 'dp'])
            ->sum('total_amount');
            
        // Online Orders (Month)
        $monthlyOnlineOrders = Order::where('source', 'online')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        return view('marketing.dashboard', compact(
            'todayOnlineOrders', 'pendingOnlineOrders', 'todayOnlineRevenue', 'monthlyOnlineOrders'
        ));
    }
}
