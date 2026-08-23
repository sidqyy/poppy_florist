<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemComponent;
use App\Models\Payment;
use App\Models\Material;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PosApiController extends Controller
{
    public function getCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function getProducts(Request $request)
    {
        $query = Product::where('is_active', true)
            ->with([
                'categories',
                'sizes',
                'sizes.variants',
                'components.material'
            ]);

        if ($request->has('category') && $request->category != '') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        $products = $query->get();
        return response()->json($products);
    }

    public function getMaterials(Request $request)
    {
        $materials = Material::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
            
        return response()->json($materials);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,qris',
            'amount_tendered' => 'required|numeric|min:0',
            'cart' => 'required|array', // The cart sent from React Native
        ]);

        $cart = $request->cart;

        if (empty($cart)) {
            return response()->json(['message' => 'Keranjang kosong!'], 400);
        }

        $baseTotalAmount = 0;
        foreach ($cart as $item) {
            $baseTotalAmount += $item['price'] * $item['qty'];
        }

        $deliveryFee = 0;
        // Simplified delivery fee logic for POS API
        if ($request->delivery_method === 'delivery' && $request->delivery_distance) {
            $distanceStr = str_replace(',', '.', $request->delivery_distance);
            $distance = floatval($distanceStr);
            $feePerKm = floatval(\App\Models\Setting::get('delivery_fee_per_km', 3000));
            $minFee = floatval(\App\Models\Setting::get('delivery_min_fee', 15000));

            if ($distance > 1) {
                $roundedDistance = ceil($distance);
                $extraKms = $roundedDistance - 1;
                $deliveryFee = $minFee + (max(0, $extraKms - 1) * $feePerKm);
                if ($deliveryFee > 0) {
                    $deliveryFee = ceil($deliveryFee / 5000) * 5000;
                }
            }
        }

        $totalAmount = $baseTotalAmount + $deliveryFee;

        if ($request->amount_tendered < $totalAmount && $request->payment_method === 'cash') {
            return response()->json(['message' => 'Uang yang dibayarkan kurang dari total tagihan akhir!'], 400);
        }

        DB::beginTransaction();

        try {
            $prefix = 'PJL';
            if ($request->scheduled_at) {
                if (Carbon::parse($request->scheduled_at)->isAfter(now()->addHours(3))) {
                    $prefix = 'PES';
                }
            }

            $latestOrder = Order::where('order_number', 'like', $prefix . '%')
                ->select('order_number')
                ->orderByRaw("CAST(REGEXP_REPLACE(order_number, '[^0-9]', '') AS UNSIGNED) DESC")
                ->lockForUpdate()
                ->first();

            $newNumber = '001';
            if ($latestOrder) {
                preg_match('/(\d+)$/', $latestOrder->order_number, $matches);
                $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            }

            $orderNumber = $prefix . $newNumber;

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'delivery_method' => $request->delivery_method,
                'delivery_address' => $request->delivery_address,
                'delivery_distance' => $request->delivery_distance ?? null,
                'delivery_fee' => $deliveryFee,
                'scheduled_at' => $request->scheduled_at,
                'notes' => 'POS Kiosk App',
                'total_amount' => $totalAmount,
                'payment_status' => 'paid',
                'status' => 'processing',
                'source' => 'offline',
                'handled_by' => 'Kiosk Walk-in',
                'user_id' => Auth::id() ?? null
            ]);

            foreach ($cart as $item) {
                $type = $item['type'] ?? 'product';
                $qty = $item['qty'] ?? 1;
                $price = $item['price'] ?? 0;
                $finalName = $item['name'] ?? 'Produk';
                $productId = ($type === 'product') ? ($item['id'] ?? null) : null;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $finalName,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $price * $qty,
                    'is_rented' => false,
                    'rental_duration' => null
                ]);

                if ($type === 'product' && $productId) {
                    $product = Product::with('components.material')->find($productId);
                    if ($product && $product->components) {
                        foreach ($product->components as $comp) {
                            $qtyToDeduct = $comp->qty * $qty;
                            $this->deductMaterial($comp->material_id, $qtyToDeduct, $order->order_number);

                            OrderItemComponent::create([
                                'order_item_id' => $orderItem->id,
                                'material_name' => $comp->material ? $comp->material->name : 'Bahan',
                                'qty' => $qtyToDeduct,
                                'unit_price' => 0, 
                                'subtotal' => 0
                            ]);
                        }
                    }
                } elseif ($type === 'material') {
                    $materialId = $item['id'];
                    $this->deductMaterial($materialId, $qty, $order->order_number);
                    
                    $mat = Material::find($materialId);
                    OrderItemComponent::create([
                        'order_item_id' => $orderItem->id,
                        'material_name' => $mat ? $mat->name : 'Bahan Eceran',
                        'qty' => $qty,
                        'unit_price' => 0,
                        'subtotal' => 0
                    ]);
                } elseif ($type === 'custom') {
                    $components = $item['components'] ?? [];
                    foreach ($components as $comp) {
                        $materialId = $comp['material_id'] ?? null;
                        $compQty = ($comp['qty'] ?? 1) * $qty;
                        
                        if ($materialId) {
                            $this->deductMaterial($materialId, $compQty, $order->order_number);
                        }
                        
                        OrderItemComponent::create([
                            'order_item_id' => $orderItem->id,
                            'material_name' => $comp['name'] ?? 'Bahan Custom',
                            'qty' => $compQty,
                            'unit_price' => 0,
                            'subtotal' => 0
                        ]);
                    }
                }
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount_tendered,
                'status' => 'verified',
                'verified_by' => Auth::id() ?? null,
                'verified_at' => now(),
                'reference_number' => 'POS-KIOSK-' . time()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    private function deductMaterial($materialId, $qty, $orderNumber)
    {
        if (!$materialId) return;
        $material = Material::find($materialId);
        if ($material) {
            $stockBefore = $material->stock;
            $qtyToDeduct = max(0, min($qty, $stockBefore));
            if ($qtyToDeduct > 0) {
                $material->decrement('stock', $qtyToDeduct);
            }
            $stockAfter = $material->fresh()->stock;

            StockMutation::create([
                'material_id' => $materialId,
                'user_id' => null,
                'type' => 'out',
                'qty' => $qtyToDeduct,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => "Penjualan via POS Kiosk - Order: " . $orderNumber
            ]);
        }
    }
}
