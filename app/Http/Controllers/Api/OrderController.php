<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    // For Florist: Get pending queue
    public function pendingQueue(Request $request)
    {
        $orders = Order::where('status', 'pending_dapur')
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json(['orders' => $orders]);
    }

    // For Florist: Mark as completed
    public function markCompleted(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'selesai';
        $order->save();
        
        return response()->json(['success' => true]);
    }

    // For Marketing: Create online order
    public function storeOnline(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'order_type' => 'required',
            'total_amount' => 'required|numeric'
        ]);

        $order = new Order();
        // Since we are simplifying, we only fill these basic fields.
        // In real app, we need order items. For kiosk simulation we just save the main row.
        $order->customer_name = $request->customer_name;
        $order->order_type = $request->order_type;
        $order->status = 'pending_dapur';
        $order->total_amount = $request->total_amount;
        $order->payment_status = 'lunas';
        $order->save();

        return response()->json(['success' => true, 'order' => $order]);
    }
}
