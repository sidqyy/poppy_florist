<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        $stats = [];

        if (in_array($user->role, ['admin', 'owner', 'it support', 'asmen'])) {
            $stats['todayRevenue'] = Order::whereDate('created_at', $today)->where('status', 'selesai')->sum('total_amount');
            $stats['todayOrders'] = Order::whereDate('created_at', $today)->count();
            $stats['processingOrders'] = Order::whereIn('status', ['pending_pembayaran', 'pending_dapur'])->count();
            $stats['urgentOrders'] = 0; // Placeholder
            $stats['totalRevenueMonth'] = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('status', 'selesai')->sum('total_amount');
        }

        if ($user->role === 'marketing') {
            $stats['todayOnlineOrders'] = Order::whereDate('created_at', $today)->where('order_type', 'like', 'marketing%')->count();
            $stats['pendingOnlineOrders'] = Order::whereIn('status', ['pending_dapur'])->where('order_type', 'like', 'marketing%')->count();
            $stats['monthlyOnlineOrders'] = Order::whereMonth('created_at', date('m'))->where('order_type', 'like', 'marketing%')->count();
            $stats['todayOnlineRevenue'] = Order::whereDate('created_at', $today)->where('order_type', 'like', 'marketing%')->where('status', 'selesai')->sum('total_amount');
        }

        if ($user->role === 'florist') {
            $stats['pendingOrders'] = Order::where('status', 'pending_dapur')->count();
            $stats['completedToday'] = Order::whereDate('created_at', $today)->where('status', 'selesai')->count();
        }

        return response()->json([
            'role' => $user->role,
            'stats' => $stats
        ]);
    }
}
