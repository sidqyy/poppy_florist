<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Order::with('user');

        // Global Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($builder) use ($q) {
                $builder->where('order_number', 'like', "%{$q}%")
                        ->orWhere('customer_name', 'like', "%{$q}%")
                        ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        // Advanced Filters
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        return view('orders.index', compact('orders'));
    }

    public function checkout($product_id)
    {
        $product = \App\Models\Product::with('components.material')->findOrFail($product_id);
        return view('orders.checkout', compact('product'));
    }

    public function store(Request $request, \App\Services\StockService $stockService)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'payment_status' => 'required|in:unpaid,dp,paid',
            'notes' => 'nullable|string'
        ]);

        $product = \App\Models\Product::with('components.material')->findOrFail($request->product_id);

        DB::beginTransaction();
        try {
            // 1. Kurangi Stok
            $stockService->reduceStockForProduct($product, 1, auth()->id());
            
            // 2. Kalkulasi Promo
            $discount = floatval($request->discount ?? 0);
            $promoId = $request->promo_id;
            
            if ($promoId) {
                $promo = \App\Models\Promo::find($promoId);
                if ($promo && $promo->is_active && $promo->used_count < ($promo->max_uses ?? PHP_INT_MAX)) {
                    // Cek min purchase
                    if ($product->total_price >= $promo->min_purchase) {
                        if ($promo->type === 'percentage') {
                            $discount = ($promo->value / 100) * $product->total_price;
                        } else {
                            $discount = $promo->value;
                        }
                    }
                }
            }

            // 3. Buat Order
            $prefix = 'PJLM';
            if ($request->scheduled_at) {
                if (\Carbon\Carbon::parse($request->scheduled_at)->isAfter(now()->addHours(3))) {
                    $prefix = 'PESM';
                }
            }
            $latestOrder = \App\Models\Order::where('order_number', 'LIKE', $prefix . '%')->orderBy('id', 'desc')->lockForUpdate()->first();
            if ($latestOrder) {
                $lastNumber = intval(substr($latestOrder->order_number, strlen($prefix)));
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }
            $orderNumber = $prefix . $newNumber;
            $deliveryFee = floatval($request->delivery_fee ?? 0);
            $totalAmount = max(0, $product->total_price + $deliveryFee - $discount);

            $order = \App\Models\Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'delivery_method' => $request->delivery_method,
                'delivery_address' => $request->delivery_address,
                'delivery_distance' => $request->delivery_distance,
                'delivery_fee' => $deliveryFee,
                'delivery_lat' => $request->delivery_lat,
                'delivery_lng' => $request->delivery_lng,
                'discount' => $discount,
                'scheduled_at' => $request->scheduled_at,
                'status' => 'pending',
                'payment_status' => $request->payment_status,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'is_urgent' => $request->has('is_urgent'),
                'estimated_time' => $request->estimated_time,
                'user_id' => auth()->id()
            ]);

            // 4. Record Promo Usage
            if ($promoId && $discount > 0) {
                \App\Models\PromoUsage::create([
                    'promo_id' => $promoId,
                    'order_id' => $order->id,
                    'discount_amount' => $discount
                ]);
                $promo->increment('used_count');
            }

            // 5. Snapshot Order Item
            $orderItem = \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'qty' => 1,
                'price' => $product->total_price,
                'subtotal' => $product->total_price
            ]);

            // 4. Snapshot Components
            foreach ($product->components as $comp) {
                \App\Models\OrderItemComponent::create([
                    'order_item_id' => $orderItem->id,
                    'material_name' => $comp->material->name,
                    'qty' => $comp->qty,
                    'unit_price' => $comp->material->price,
                    'subtotal' => $comp->material->price * $comp->qty
                ]);
            }

            DB::commit();
            
            \App\Services\AuditService::log('Membuat Pesanan Offline', null, $order->toArray());

            return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat pesanan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $order = \App\Models\Order::with(['items.components', 'user'])->findOrFail($id);
        
        $waLinks = [];
        if (!empty($order->customer_phone)) {
            $waService = new \App\Services\WhatsAppService();
            $waLinks = [
                'received' => $waService->getWaLink($order, 'received'),
                'payment_verified' => $waService->getWaLink($order, 'payment_verified'),
                'processing' => $waService->getWaLink($order, 'processing'),
                'ready' => $waService->getWaLink($order, 'ready'),
                'delivery' => $waService->getWaLink($order, 'delivery'),
                'completed' => $waService->getWaLink($order, 'completed'),
            ];
        }

        return view('orders.show', compact('order', 'waLinks'));
    }

    public function createOnline()
    {
        return view('orders.online_create');
    }

    public function storeOnline(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'order_prefix' => 'required|in:PESW,PESM,PJLM,PES,PJL',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'payment_status' => 'required|in:unpaid,dp,paid',
            'payment_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'product_name' => 'required|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'reference_image' => 'nullable|image|max:2048',
            'greeting_card' => 'nullable|string',
            'notes' => 'required|string'
        ]);

        $imagePath = null;
        if ($request->hasFile('reference_image')) {
            $imagePath = $request->file('reference_image')->store('references', 'public');
        }
        
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        $prefix = $request->order_prefix;
        $latestOrder = \App\Models\Order::where('order_number', 'LIKE', $prefix . '%')->orderBy('id', 'desc')->lockForUpdate()->first();
        if ($latestOrder) {
            $lastNumber = intval(substr($latestOrder->order_number, strlen($prefix)));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        $orderNumber = $prefix . $newNumber;
            
        $deliveryFee = floatval($request->delivery_fee ?? 0);
        $totalAmount = floatval($request->total_price ?? 0) + $deliveryFee;
        
        // Kalkulasi Promo
        $discount = floatval($request->discount ?? 0);
        $promoId = $request->promo_id;
        
        if ($promoId) {
            $promo = \App\Models\Promo::find($promoId);
            if ($promo && $promo->is_active && $promo->used_count < ($promo->max_uses ?? PHP_INT_MAX)) {
                if ($totalAmount >= $promo->min_purchase) {
                    if ($promo->type === 'percentage') {
                        $discount = ($promo->value / 100) * $totalAmount;
                    } else {
                        $discount = $promo->value;
                    }
                }
            }
        }
        
        $finalTotalAmount = max(0, $totalAmount - $discount);

        $order = \App\Models\Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'external_id' => null,
            'recipient_name' => $request->recipient_name,
            'recipient_phone' => $request->recipient_phone,
            'delivery_method' => $request->delivery_method,
            'delivery_address' => $request->delivery_address,
            'delivery_distance' => $request->delivery_distance,
            'delivery_fee' => $deliveryFee,
            'delivery_lat' => $request->delivery_lat,
            'delivery_lng' => $request->delivery_lng,
            'discount' => $discount,
            'scheduled_at' => $request->scheduled_at,
            'status' => 'pending',
            'payment_status' => $request->payment_status,
            'payment_proof' => $paymentProofPath,
            'total_amount' => $finalTotalAmount,
            'budget' => 0,
            'reference_image' => $imagePath,
            'product_name' => $request->product_name,
            'greeting_card' => $request->greeting_card,
            'notes' => $request->notes,
            'source' => 'online',
            'is_urgent' => $request->has('is_urgent'),
            'estimated_time' => $request->estimated_time,
            'user_id' => auth()->id()
        ]);

        $initialPayment = floatval($request->initial_payment ?? 0);
        if ($request->payment_status == 'paid') {
            $initialPayment = $finalTotalAmount;
        }

        if ($initialPayment > 0) {
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'amount' => $initialPayment,
                'payment_method' => 'Online/Transfer',
                'proof_image' => $paymentProofPath,
                'status' => 'verified',
                'verified_at' => now(),
                'notes' => 'Pembayaran awal via Form Marketing'
            ]);
        }

        // Record Promo Usage
        if ($promoId && $discount > 0) {
            \App\Models\PromoUsage::create([
                'promo_id' => $promoId,
                'order_id' => $order->id,
                'discount_amount' => $discount
            ]);
            $promo->increment('used_count');
        }

        \App\Services\AuditService::log('Membuat Pesanan Online', null, $order->toArray());

        return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan Online berhasil diinput!');
    }

    public function kitchen()
    {
        $orders = \App\Models\Order::where('status', '!=', 'completed')
            ->orderByDesc('is_urgent')
            ->orderBy('scheduled_at', 'asc')
            ->get();
            
        return view('orders.kitchen', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,ready,completed,cancelled'
        ]);
        
        $oldData = $order->toArray();
        $order->update(['status' => $request->status]);
        $newData = $order->toArray();
        
        \App\Services\AuditService::log('Mengubah Status Pesanan', ['status' => $oldData['status']], ['status' => $newData['status']]);

        $updateData = [];

        // Track timeline
        if ($request->status == 'processing' && !$order->started_at) {
            $updateData['started_at'] = now();
        } elseif ($request->status == 'ready' && !$order->completed_at) {
            $updateData['completed_at'] = now();
        }
        
        $oldStatus = $order->status;
        $order->update($updateData);

        // Log history
        if ($oldStatus != $request->status) {
            \App\Models\OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'action' => 'status_update',
                'notes' => 'Status diubah dari ' . $oldStatus . ' menjadi ' . $request->status
            ]);
        }
        
        return back()->with('success', 'Status pesanan diperbarui menjadi: ' . $request->status);
    }

    public function updateFloristNotes(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        
        $request->validate([
            'florist_notes' => 'nullable|string'
        ]);
        
        $order->update(['florist_notes' => $request->florist_notes]);
        
        return back()->with('success', 'Catatan florist berhasil disimpan.');
    }

    public function calculateOngkir(Request $request)
    {
        // Replace comma with dot to handle Indonesian locale decimals
        $distanceStr = str_replace(',', '.', $request->query('distance', '0'));
        $distance = floatval($distanceStr);
        
        $feePerKm = floatval(\App\Models\Setting::get('delivery_fee_per_km', 3000));
        $minFee = floatval(\App\Models\Setting::get('delivery_min_fee', 15000));
        $maxRadius = floatval(\App\Models\Setting::get('delivery_max_radius', 25));

        if ($distance > $maxRadius) {
            return response()->json([
                'status' => 'error',
                'message' => 'Area di luar jangkauan pengiriman! Maksimal radius adalah ' . $maxRadius . ' km.',
                'fee' => 0
            ]);
        }

        if ($distance <= 1) {
            $finalFee = 0;
        } else {
            $roundedDistance = ceil($distance);
            $extraKms = $roundedDistance - 1;
            $finalFee = $minFee + (max(0, $extraKms - 1) * $feePerKm);
            
            // Pembulatan ke atas kelipatan 5.000
            if ($finalFee > 0) {
                $finalFee = ceil($finalFee / 5000) * 5000;
            }
        }

        return response()->json([
            'status' => 'success',
            'distance' => $distance,
            'fee' => $finalFee,
            'formatted_fee' => 'Rp ' . number_format($finalFee, 0, ',', '.')
        ]);
    }

    public function printReceipt($id)
    {
        $order = \App\Models\Order::with(['items.components', 'payments.verifier', 'user'])->findOrFail($id);
        
        // Cek apakah sudah lunas
        if ($order->payment_status !== 'paid') {
            return back()->withErrors(['error' => 'Nota hanya bisa dicetak jika pesanan sudah LUNAS.']);
        }

        \App\Services\AuditService::log('Mencetak Ulang Nota', null, ['order_number' => $order->order_number]);

        return view('orders.print', compact('order'));
    }

    public function checkPromo(Request $request)
    {
        $code = $request->query('code');
        $subtotal = floatval($request->query('subtotal', 0));

        if (!$code) return response()->json(['status' => 'error', 'message' => 'Kode voucher kosong.']);

        $promo = \App\Models\Promo::where('code', $code)->first();

        if (!$promo) {
            return response()->json(['status' => 'error', 'message' => 'Kode voucher tidak ditemukan.']);
        }

        if (!$promo->is_active) {
            return response()->json(['status' => 'error', 'message' => 'Voucher sudah tidak aktif.']);
        }

        if ($promo->start_date && now()->lt($promo->start_date)) {
            return response()->json(['status' => 'error', 'message' => 'Voucher belum bisa digunakan.']);
        }

        if ($promo->end_date && now()->gt($promo->end_date)) {
            return response()->json(['status' => 'error', 'message' => 'Voucher sudah kadaluwarsa.']);
        }

        if ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
            return response()->json(['status' => 'error', 'message' => 'Kuota voucher sudah habis.']);
        }

        if ($promo->min_purchase > 0 && $subtotal < $promo->min_purchase) {
            return response()->json(['status' => 'error', 'message' => 'Minimal belanja untuk voucher ini adalah Rp ' . number_format($promo->min_purchase, 0, ',', '.')]);
        }

        // Kalkulasi diskon
        $discountAmount = 0;
        if ($promo->type === 'percentage') {
            $discountAmount = ($promo->value / 100) * $subtotal;
        } else {
            $discountAmount = $promo->value;
        }

        // Jangan melebihi subtotal
        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        return response()->json([
            'status' => 'success',
            'promo_id' => $promo->id,
            'discount_amount' => $discountAmount,
            'message' => 'Voucher berhasil diterapkan!'
        ]);
    }

    public function checkNewOrders(Request $request)
    {
        $lastCheck = $request->query('last_check'); // Timestamp of last check
        if (!$lastCheck) {
            return response()->json(['has_new' => false]);
        }

        $newOrdersCount = \App\Models\Order::where('created_at', '>', date('Y-m-d H:i:s', $lastCheck))
            ->where('source', 'online')
            ->count();

        return response()->json([
            'has_new' => $newOrdersCount > 0,
            'count' => $newOrdersCount
        ]);
    }
}
