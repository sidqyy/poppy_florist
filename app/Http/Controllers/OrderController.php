<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private function paymentStatusLabel(?string $status)
    {
        return match ($status) {
            'paid_qris' => 'LUNAS QRIS',
            'paid_tf' => 'LUNAS TF',
            'dp' => 'DP',
            'unpaid' => 'BELUM LUNAS',
            'paid' => 'LUNAS',
            default => strtoupper($status ?? '-'),
        };
    }

    public function index(Request $request)
    {
        $query = \App\Models\Order::with('user');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function checkout(int $product_id)
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
            'payment_status' => 'required|in:unpaid,dp,paid_qris,paid_tf',
            'notes' => 'nullable|string',
        ]);

        $product = \App\Models\Product::with('components.material')->findOrFail($request->product_id);

        DB::beginTransaction();

        try {
            $discount = floatval($request->discount ?? 0);
            $promoId = $request->promo_id;

            if ($promoId) {
                $promo = \App\Models\Promo::find($promoId);

                if ($promo && $promo->is_active && $promo->used_count < ($promo->max_uses ?? PHP_INT_MAX)) {
                    if ($product->total_price >= $promo->min_purchase) {
                        if ($promo->type === 'percentage') {
                            $discount = ($promo->value / 100) * $product->total_price;
                        } else {
                            $discount = $promo->value;
                        }
                    }
                }
            }

            $prefix = 'PJLM';

            if ($request->scheduled_at) {
                if (\Carbon\Carbon::parse($request->scheduled_at)->isAfter(now()->addHours(3))) {
                    $prefix = 'PESM';
                }
            }

            $latestOrder = \App\Models\Order::where('order_number', 'LIKE', $prefix . '%')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

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
                'user_id' => Auth::id(),
            ]);

            if ($promoId && $discount > 0) {
                \App\Models\PromoUsage::create([
                    'promo_id' => $promoId,
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                ]);

                $promo->increment('used_count');
            }

            $orderItem = \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'qty' => 1,
                'price' => $product->total_price,
                'subtotal' => $product->total_price,
            ]);

            foreach ($product->components as $comp) {
                \App\Models\OrderItemComponent::create([
                    'order_item_id' => $orderItem->id,
                    'material_id' => $comp->material->id,
                    'material_name' => $comp->material->name,
                    'qty' => $comp->qty,
                    'unit_price' => $comp->material->price,
                    'subtotal' => $comp->material->price * $comp->qty,
                ]);
            }

            DB::commit();

            \App\Services\AuditService::log('Membuat Pesanan Offline', null, $order->toArray());

            return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Gagal membuat pesanan: ' . $e->getMessage(),
            ])->withInput();
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
        $materials = \App\Models\Material::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('orders.online_create', compact('materials'));
    }

    public function storeOnline(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'order_prefix' => 'required|in:PESW,PESM,PJLM,PES,PJL',
            'manual_order_number' => 'nullable|required_if:order_prefix,PESW|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'payment_status' => 'required|in:paid_qris,paid_tf,dp,unpaid',
            'payment_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'product_name' => 'required|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'reference_image' => 'nullable|image|max:2048',
            'greeting_card' => 'nullable|string',
            'notes' => 'required|string',
            'components' => 'nullable|array',
            'components.*.material_id' => 'nullable|exists:materials,id',
            'components.*.qty' => 'nullable|integer|min:1',
            'components.*.price_type' => 'nullable|in:arrangement,stem',
            'components.*.color' => 'nullable|string|max:100',
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

        if ($prefix === 'PESW') {
            $manualNumber = trim($request->manual_order_number);

            if (str_starts_with(strtoupper($manualNumber), 'PESW-')) {
                $manualNumber = substr($manualNumber, 5);
            } elseif (str_starts_with(strtoupper($manualNumber), 'PESW')) {
                $manualNumber = substr($manualNumber, 4);
            }

            $orderNumber = 'PESW' . $manualNumber;

            $exists = \App\Models\Order::where('order_number', $orderNumber)->exists();

            if ($exists) {
                return back()->withErrors([
                    'manual_order_number' => 'Nomor order manual "' . $orderNumber . '" sudah terpakai di sistem. Harap gunakan nomor order unik dari website Anda.',
                ])->withInput();
            }
        } else {
            $latestOrder = \App\Models\Order::where('order_number', 'LIKE', $prefix . '%')
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            if ($latestOrder) {
                $lastNumber = intval(substr($latestOrder->order_number, strlen($prefix)));
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }

            $orderNumber = $prefix . $newNumber;
        }

        $deliveryFee = floatval($request->delivery_fee ?? 0);
        $totalAmount = floatval($request->total_price ?? 0) + $deliveryFee;

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
            'user_id' => Auth::id(),
        ]);

        $orderItem = \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => null,
            'product_name' => $request->product_name,
            'qty' => 1,
            'price' => $finalTotalAmount,
            'subtotal' => $finalTotalAmount,
        ]);

        if ($request->has('components')) {
            foreach ($request->components as $component) {
                if (empty($component['material_id']) || empty($component['qty'])) {
                    continue;
                }

                $material = \App\Models\Material::find($component['material_id']);

                if (!$material) {
                    continue;
                }

                $priceType = $component['price_type'] ?? 'arrangement';

                if ($priceType === 'stem') {
                    $unitPrice = $material->price_stem > 0
                        ? $material->price_stem
                        : $material->price;
                } else {
                    $unitPrice = $material->price_arrangement > 0
                        ? $material->price_arrangement
                        : $material->price;
                }

                $color = null;

                if ($material->type === 'flower_fresh' && !empty($component['color'])) {
                    $color = trim($component['color']);
                }

                \App\Models\OrderItemComponent::create([
                    'order_item_id' => $orderItem->id,
                    'material_id' => $material->id,
                    'material_name' => $material->name,
                    'color' => $color,
                    'qty' => $component['qty'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $component['qty'],
                ]);
            }
        }

        $initialPayment = floatval($request->initial_payment ?? 0);

        if (in_array($request->payment_status, ['paid_qris', 'paid_tf'])) {
            $initialPayment = $finalTotalAmount;
        }

        if ($initialPayment > 0) {
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'amount' => $initialPayment,
                'payment_method' => $request->payment_status === 'paid_qris' ? 'QRIS' : 'Transfer',
                'proof_image' => $paymentProofPath,
                'status' => 'verified',
                'verified_at' => now(),
                'notes' => 'Pembayaran awal via Form Marketing',
            ]);
        }

        if ($promoId && $discount > 0) {
            \App\Models\PromoUsage::create([
                'promo_id' => $promoId,
                'order_id' => $order->id,
                'discount_amount' => $discount,
            ]);

            $promo->increment('used_count');
        }

        \App\Services\AuditService::log('Membuat Pesanan Online', null, $order->toArray());

        try {
            $subscriptions = \App\Models\PushSubscription::all();

            if (count($subscriptions) > 0 && env('VAPID_PUBLIC_KEY')) {
                $auth = [
                    'VAPID' => [
                        'subject' => 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
                        'publicKey' => env('VAPID_PUBLIC_KEY'),
                        'privateKey' => env('VAPID_PRIVATE_KEY'),
                    ],
                ];

                $webPush = new \Minishlink\WebPush\WebPush($auth);

                foreach ($subscriptions as $subscription) {
                    $webPush->queueNotification(
                        \Minishlink\WebPush\Subscription::create([
                            'endpoint' => $subscription->endpoint,
                            'keys' => [
                                'p256dh' => $subscription->public_key,
                                'auth' => $subscription->auth_token,
                            ],
                        ]),
                        json_encode([
                            'title' => 'Pesanan Baru!',
                            'body' => 'Ada pesanan baru yang perlu dirangkai',
                        ], JSON_THROW_ON_ERROR)
                    );
                }

                $webPush->flush();
            }
        } catch (\Exception $e) {
        }

        return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan Online berhasil diinput!');
    }

    public function editOnline(string $id)
    {
        $order = \App\Models\Order::with('items.components')->findOrFail($id);

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->route('orders.show', $order->id)->withErrors(['error' => 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diedit.']);
        }

        $materials = \App\Models\Material::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('orders.online_edit', compact('order', 'materials'));
    }

    public function updateOnline(Request $request, string $id)
    {
        $order = \App\Models\Order::with('items.components')->findOrFail($id);

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->route('orders.show', $order->id)->withErrors(['error' => 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diedit.']);
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'product_name' => 'required|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'reference_image' => 'nullable|image|max:2048',
            'greeting_card' => 'nullable|string',
            'notes' => 'required|string',
            'components' => 'nullable|array',
            'components.*.material_id' => 'nullable|exists:materials,id',
            'components.*.qty' => 'nullable|integer|min:1',
            'components.*.price_type' => 'nullable|in:arrangement,stem',
            'components.*.color' => 'nullable|string|max:100',
        ]);

        $imagePath = $order->reference_image;

        if ($request->hasFile('reference_image')) {
            $imagePath = $request->file('reference_image')->store('references', 'public');
        }

        $deliveryFee = floatval($request->delivery_fee ?? 0);
        $totalAmount = floatval($request->total_price ?? 0) + $deliveryFee;

        // Preserve original discount logic (readonly in edit form)
        $finalTotalAmount = max(0, $totalAmount - $order->discount);

        $deductedStates = ['processing', 'ready'];
        $isDeducted = in_array($order->status, $deductedStates);

        DB::beginTransaction();

        try {
            // 1. Return stock for old components if deducted
            if ($isDeducted) {
                foreach ($order->items as $item) {
                    foreach ($item->components as $component) {
                        if ($component->material_id) {
                            $material = \App\Models\Material::find($component->material_id);
                            if ($material) {
                                $stockBefore = $material->stock;
                                $material->increment('stock', $component->qty);
                                $stockAfter = $material->fresh()->stock;
                                
                                \App\Models\StockMutation::create([
                                    'material_id' => $material->id,
                                    'user_id' => Auth::id(),
                                    'type' => 'in',
                                    'qty' => $component->qty,
                                    'stock_before' => $stockBefore,
                                    'stock_after' => $stockAfter,
                                    'notes' => 'Pengembalian stok (Edit Data) dari pesanan ' . $order->order_number,
                                ]);
                            }
                        }
                    }
                }
            }

            // 2. Delete old components
            foreach ($order->items as $item) {
                $item->components()->delete();
            }
            $order->items()->delete();

            // 3. Update order data
            $order->update([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'delivery_method' => $request->delivery_method,
                'delivery_address' => $request->delivery_address,
                'delivery_distance' => $request->delivery_distance,
                'delivery_fee' => $deliveryFee,
                'delivery_lat' => $request->delivery_lat,
                'delivery_lng' => $request->delivery_lng,
                'scheduled_at' => $request->scheduled_at,
                'total_amount' => $finalTotalAmount,
                'reference_image' => $imagePath,
                'product_name' => $request->product_name,
                'greeting_card' => $request->greeting_card,
                'notes' => $request->notes,
                'is_urgent' => $request->has('is_urgent'),
                'estimated_time' => $request->estimated_time,
            ]);

            // 4. Create new item and components
            $orderItem = \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null,
                'product_name' => $request->product_name,
                'qty' => 1,
                'price' => $finalTotalAmount,
                'subtotal' => $finalTotalAmount,
            ]);

            if ($request->has('components')) {
                foreach ($request->components as $component) {
                    if (empty($component['material_id']) || empty($component['qty'])) {
                        continue;
                    }

                    $material = \App\Models\Material::find($component['material_id']);

                    if (!$material) {
                        continue;
                    }

                    $priceType = $component['price_type'] ?? 'arrangement';

                    if ($priceType === 'stem') {
                        $unitPrice = $material->price_stem > 0
                            ? $material->price_stem
                            : $material->price;
                    } else {
                        $unitPrice = $material->price_arrangement > 0
                            ? $material->price_arrangement
                            : $material->price;
                    }

                    $color = null;

                    if ($material->type === 'flower_fresh' && !empty($component['color'])) {
                        $color = trim($component['color']);
                    }

                    \App\Models\OrderItemComponent::create([
                        'order_item_id' => $orderItem->id,
                        'material_id' => $material->id,
                        'material_name' => $material->name,
                        'color' => $color,
                        'qty' => $component['qty'],
                        'unit_price' => $unitPrice,
                        'subtotal' => $unitPrice * $component['qty'],
                    ]);

                    // 5. Deduct stock for new components if deducted
                    if ($isDeducted) {
                        $stockBefore = $material->stock;
                        if ($material->stock < $component['qty']) {
                            DB::rollBack();
                            return back()->withErrors(['error' => 'Stok ' . $material->name . ' tidak mencukupi untuk diubah. Tersedia: ' . $material->stock]);
                        }
                        
                        $material->decrement('stock', $component['qty']);
                        $stockAfter = $material->fresh()->stock;
                        
                        \App\Models\StockMutation::create([
                            'material_id' => $material->id,
                            'user_id' => Auth::id(),
                            'type' => 'out',
                            'qty' => $component['qty'],
                            'stock_before' => $stockBefore,
                            'stock_after' => $stockAfter,
                            'notes' => 'Penggunaan stok (Edit Data) untuk pesanan ' . $order->order_number,
                        ]);
                    }
                }
            }

            \App\Services\AuditService::log('Mengubah Pesanan Online', null, $order->toArray());
            
            \App\Models\OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'old_status' => $order->status,
                'new_status' => $order->status,
                'action' => 'edit_data',
                'notes' => 'Data pesanan dan/atau komponen diubah oleh ' . (Auth::user()->name ?? 'Sistem'),
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order->id)->with('success', 'Data pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Gagal memperbarui pesanan: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function exportExcel(Request $request)
    {
        $query = \App\Models\Order::with('user');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $fileDate = $request->date ?: now()->format('Y-m-d');
        $fileName = 'data_pesanan_' . $fileDate . '.xls';

        $headers = [
            "Content-Type" => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        return response()->stream(function () use ($orders) {
            echo "\xEF\xBB\xBF";
            echo "<table border='1'>";
            echo "<tr>
                    <th>No</th>
                    <th>Order ID</th>
                    <th>Nama Pemesan</th>
                    <th>No HP</th>
                    <th>Nama Penerima</th>
                    <th>No HP Penerima</th>
                    <th>Tanggal Pesan</th>
                    <th>Jadwal Kirim/Ambil</th>
                    <th>Metode</th>
                    <th>Alamat</th>
                    <th>Ongkir</th>
                    <th>Diskon</th>
                    <th>Total</th>
                    <th>Status Pembayaran</th>
                    <th>Status Pesanan</th>
                    <th>Sumber</th>
                    <th>Diinput Oleh</th>
                  </tr>";

            foreach ($orders as $index => $order) {
                echo "<tr>";
                echo "<td>" . ($index + 1) . "</td>";
                echo "<td>" . e($order->order_number) . "</td>";
                echo "<td>" . e($order->customer_name) . "</td>";
                echo "<td>" . e($order->customer_phone ?? '-') . "</td>";
                echo "<td>" . e($order->recipient_name ?? '-') . "</td>";
                echo "<td>" . e($order->recipient_phone ?? '-') . "</td>";
                echo "<td>" . e($order->created_at ? $order->created_at->format('d/m/Y H:i') : '-') . "</td>";
                echo "<td>" . e($order->scheduled_at ? \Carbon\Carbon::parse($order->scheduled_at)->format('d/m/Y H:i') : '-') . "</td>";
                echo "<td>" . e($order->delivery_method == 'pickup' ? 'Ambil di Toko' : 'Diantar') . "</td>";
                echo "<td>" . e($order->delivery_address ?? '-') . "</td>";
                echo "<td>" . number_format($order->delivery_fee ?? 0, 0, ',', '.') . "</td>";
                echo "<td>" . number_format($order->discount ?? 0, 0, ',', '.') . "</td>";
                echo "<td>" . number_format($order->total_amount ?? 0, 0, ',', '.') . "</td>";
                echo "<td>" . e($this->paymentStatusLabel($order->payment_status)) . "</td>";
                echo "<td>" . e(strtoupper($order->status ?? '-')) . "</td>";
                echo "<td>" . e(strtoupper($order->source ?? '-')) . "</td>";
                echo "<td>" . e($order->user->name ?? $order->handled_by ?? 'System') . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        }, 200, $headers);
    }

    public function kitchen()
    {
        $orders = \App\Models\Order::where('status', '!=', 'completed')
            ->orderByDesc('is_urgent')
            ->orderByDesc('created_at')
            ->get();

        return view('orders.kitchen', compact('orders'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $order = \App\Models\Order::with('items.components.material')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,ready,completed,cancelled',
        ]);

        $oldData = $order->toArray();
        $oldStatus = $order->status;

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->status == 'processing' && !$order->started_at) {
            $updateData['started_at'] = now();
        } elseif ($request->status == 'ready' && !$order->completed_at) {
            $updateData['completed_at'] = now();
        }

        DB::beginTransaction();

        try {
            $deductedStates = ['processing', 'ready', 'completed'];
            $wasDeducted = in_array($oldStatus, $deductedStates);
            $willBeDeducted = in_array($request->status, $deductedStates);

            if ($willBeDeducted && !$wasDeducted) {
                foreach ($order->items as $item) {
                    foreach ($item->components as $component) {
                        if ($component->material && $component->material->stock < $component->qty) {
                            DB::rollBack();

                            return back()->withErrors([
                                'error' => 'Stok ' . $component->material->name . ' tidak mencukupi. Stok tersedia: ' . $component->material->stock . ', dibutuhkan: ' . $component->qty,
                            ]);
                        }
                    }
                }

                foreach ($order->items as $item) {
                    foreach ($item->components as $component) {
                        if ($component->material) {
                            $material = $component->material;
                            $stockBefore = $material->stock;

                            $material->decrement('stock', $component->qty);
                            $stockAfter = $material->fresh()->stock;

                            \App\Models\StockMutation::create([
                                'material_id' => $material->id,
                                'user_id' => Auth::id(),
                                'type' => 'out',
                                'qty' => $component->qty,
                                'stock_before' => $stockBefore,
                                'stock_after' => $stockAfter,
                                'notes' => 'Digunakan untuk pesanan ' . $order->order_number,
                            ]);
                        }
                    }
                }
            } elseif (!$willBeDeducted && $wasDeducted) {
                foreach ($order->items as $item) {
                    foreach ($item->components as $component) {
                        if ($component->material) {
                            $material = $component->material;
                            $stockBefore = $material->stock;

                            $material->increment('stock', $component->qty);
                            $stockAfter = $material->fresh()->stock;

                            \App\Models\StockMutation::create([
                                'material_id' => $material->id,
                                'user_id' => Auth::id(),
                                'type' => 'in',
                                'qty' => $component->qty,
                                'stock_before' => $stockBefore,
                                'stock_after' => $stockAfter,
                                'notes' => 'Pengembalian stok (Restock) dari pesanan batal/tunda ' . $order->order_number,
                            ]);
                        }
                    }
                }
            }

            $order->update($updateData);
            $newData = $order->fresh()->toArray();

            \App\Services\AuditService::log('Mengubah Status Pesanan', ['status' => $oldData['status']], ['status' => $newData['status']]);

            if ($oldStatus != $request->status) {
                \App\Models\OrderHistory::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'action' => 'status_update',
                    'notes' => 'Status diubah dari ' . $oldStatus . ' menjadi ' . $request->status,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Status pesanan diperbarui menjadi: ' . $request->status);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Gagal memperbarui status pesanan: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateFloristNotes(Request $request, int $id)
    {
        $order = \App\Models\Order::findOrFail($id);

        $request->validate([
            'florist_notes' => 'nullable|string',
        ]);

        $order->update([
            'florist_notes' => $request->florist_notes,
        ]);

        return back()->with('success', 'Catatan florist berhasil disimpan.');
    }

    public function calculateOngkir(Request $request)
    {
        $distanceStr = str_replace(',', '.', $request->query('distance', '0'));
        $distance = floatval($distanceStr);

        $feePerKm = floatval(\App\Models\Setting::get('delivery_fee_per_km', 3000));
        $minFee = floatval(\App\Models\Setting::get('delivery_min_fee', 15000));
        $maxRadius = floatval(\App\Models\Setting::get('delivery_max_radius', 25));

        if ($distance > $maxRadius) {
            return response()->json([
                'status' => 'error',
                'message' => 'Area di luar jangkauan pengiriman! Maksimal radius adalah ' . $maxRadius . ' km.',
                'fee' => 0,
            ]);
        }

        if ($distance <= 1) {
            $finalFee = 0;
        } else {
            $roundedDistance = ceil($distance);
            $extraKms = $roundedDistance - 1;
            $finalFee = $minFee + (max(0, $extraKms - 1) * $feePerKm);

            if ($finalFee > 0) {
                $finalFee = ceil($finalFee / 5000) * 5000;
            }
        }

        return response()->json([
            'status' => 'success',
            'distance' => $distance,
            'fee' => $finalFee,
            'formatted_fee' => 'Rp ' . number_format($finalFee, 0, ',', '.'),
        ]);
    }

    public function printReceipt(int $id)
    {
        $order = \App\Models\Order::with(['items.components', 'payments.verifier', 'user'])->findOrFail($id);

        if (!in_array($order->payment_status, ['paid_qris', 'paid_tf', 'paid', 'dp'])) {
            return back()->withErrors([
                'error' => 'Nota hanya bisa dicetak jika pesanan sudah LUNAS.',
            ]);
        }

        \App\Services\AuditService::log('Mencetak Ulang Nota', null, [
            'order_number' => $order->order_number,
        ]);

        $order->update(['is_printed' => true]);

        return view('orders.print', compact('order'));
    }

    public function checkPromo(Request $request)
    {
        $code = $request->query('code');
        $subtotal = floatval($request->query('subtotal', 0));

        if (!$code) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode voucher kosong.',
            ]);
        }

        $promo = \App\Models\Promo::where('code', $code)->first();

        if (!$promo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode voucher tidak ditemukan.',
            ]);
        }

        if (!$promo->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voucher sudah tidak aktif.',
            ]);
        }

        if ($promo->start_date && now()->lt($promo->start_date)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voucher belum bisa digunakan.',
            ]);
        }

        if ($promo->end_date && now()->gt($promo->end_date)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Voucher sudah kadaluwarsa.',
            ]);
        }

        if ($promo->max_uses && $promo->used_count >= $promo->max_uses) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuota voucher sudah habis.',
            ]);
        }

        if ($promo->min_purchase > 0 && $subtotal < $promo->min_purchase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Minimal belanja untuk voucher ini adalah Rp ' . number_format($promo->min_purchase, 0, ',', '.'),
            ]);
        }

        $discountAmount = 0;

        if ($promo->type === 'percentage') {
            $discountAmount = ($promo->value / 100) * $subtotal;
        } else {
            $discountAmount = $promo->value;
        }

        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        return response()->json([
            'status' => 'success',
            'promo_id' => $promo->id,
            'discount_amount' => $discountAmount,
            'message' => 'Voucher berhasil diterapkan!',
        ]);
    }

    public function checkNewOrders(Request $request)
    {
        $lastCheck = $request->query('last_check');
        $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();

        if (!$lastCheck) {
            return response()->json([
                'has_new' => false,
                'count' => $pendingOrdersCount,
                'pending' => $pendingOrdersCount,
            ]);
        }

        $newOrdersCount = \App\Models\Order::where('created_at', '>', date('Y-m-d H:i:s', $lastCheck))
            ->where('source', 'online')
            ->count();

        return response()->json([
            'has_new' => $newOrdersCount > 0,
            'count' => $newOrdersCount,
            'pending' => $pendingOrdersCount,
        ]);
    }
}