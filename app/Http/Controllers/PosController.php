<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Material;
use App\Models\StockMutation;
use App\Models\OrderItemComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PosController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    public function kiosk()
    {
        return view('pos.kiosk');
    }

    public function catalog(Request $request)
    {
        $categories = \App\Models\Category::all();
        $query = Product::where('is_active', true)->with('categories');
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }
        $products = $query->paginate(10)->withQueryString();
        
        $data = $this->getCartData();
        $data['categories'] = $categories;
        $data['products'] = $products;
        
        return view('pos.catalog', $data);
    }

    public function materials(string $type)
    {
        $materials = Material::where('type', $type)->where('is_active', true)->get();
        $title = $type == 'flower_fresh' ? 'Bunga Batangan' : 'Bunga Artificial';
        
        $data = $this->getCartData();
        $data['materials'] = $materials;
        $data['title'] = $title;
        $data['type'] = $type;
        
        return view('pos.materials', $data);
    }

    public function custom()
    {
        $materials = Material::where('is_active', true)->orderBy('type')->orderBy('name')->get()->groupBy('type');
        
        $data = $this->getCartData();
        $data['groupedMaterials'] = $materials;
        
        return view('pos.custom', $data);
    }

    private function getCartData()
    {
        $cart = Session::get('pos_cart', []);
        $totalItems = 0;
        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalItems += $item['qty'];
            $totalPrice += $item['price'] * $item['qty'];
        }
        return compact('cart', 'totalItems', 'totalPrice');
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = Session::get('pos_cart', []);
        $price = $product->total_price;
        $cartKey = 'prod_' . $product->id;
        
        $isRented = false;
        $rentalDuration = null;

        if ($request->has('is_rented') && $request->is_rented == '1') {
            $isRented = true;
            $rentalDuration = max(1, intval($request->rental_duration));
            $price = $product->rental_price_per_day * $rentalDuration;
            $cartKey = 'prod_' . $product->id . '_rent_' . $rentalDuration;
        } else {
            if ($request->has('custom_price') && $product->price_type == 'range') {
                $price = $request->custom_price;
                $cartKey = 'prod_' . $product->id . '_' . $price;
            }
        }

        if(isset($cart[$cartKey])) {
            $cart[$cartKey]['qty']++;
        } else {
            $cart[$cartKey] = [
                "id" => $product->id,
                "type" => "product",
                "name" => $product->name,
                "qty" => 1,
                "price" => $price,
                "image" => $product->image,
                "is_rented" => $isRented,
                "rental_duration" => $rentalDuration
            ];
        }

        Session::put('pos_cart', $cart);
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function addMaterialToCart(Request $request)
    {
        $material = Material::findOrFail($request->material_id);
        $cart = Session::get('pos_cart', []);
        $cartKey = 'mat_' . $material->id;

        if(isset($cart[$cartKey])) {
            $cart[$cartKey]['qty']++;
        } else {
            $cart[$cartKey] = [
                "id" => $material->id,
                "type" => "material",
                "name" => $material->name . ' (Eceran)',
                "qty" => 1,
                "price" => $material->price,
                "image" => $material->image
            ];
        }

        Session::put('pos_cart', $cart);
        return redirect()->back()->with('success', 'Bahan berhasil ditambahkan ke keranjang');
    }

    public function addCustomToCart(Request $request)
    {
        $request->validate([
            'custom_name' => 'required|string|max:255',
            'custom_notes' => 'nullable|string',
            'materials' => 'required|array',
            'materials.*' => 'integer|min:0',
            'extra_items' => 'nullable|array',
            'extra_items.*.name' => 'required|string',
            'extra_items.*.price' => 'required|numeric|min:0',
            'extra_items.*.qty' => 'required|integer|min:1'
        ]);

        $components = [];
        $totalPrice = 0;

        // Proses bahan baku sistem
        foreach($request->materials as $matId => $qty) {
            if($qty > 0) {
                $material = Material::find($matId);
                if($material) {
                    $subtotal = $material->price * $qty;
                    $totalPrice += $subtotal;
                    $components[] = [
                        'material_id' => $material->id,
                        'name' => $material->name,
                        'qty' => $qty,
                        'price' => $material->price
                    ];
                }
            }
        }

        // Proses produk tambahan ad-hoc
        if($request->extra_items) {
            foreach($request->extra_items as $extra) {
                $qty = intval($extra['qty']);
                $price = floatval($extra['price']);
                if($qty > 0) {
                    $subtotal = $price * $qty;
                    $totalPrice += $subtotal;
                    $components[] = [
                        'material_id' => null, // Tidak masuk pemotongan stok bahan
                        'name' => $extra['name'],
                        'qty' => $qty,
                        'price' => $price
                    ];
                }
            }
        }

        if(empty($components)) {
            return redirect()->back()->with('error', 'Pilih minimal satu bahan untuk custom buket');
        }

        $cart = Session::get('pos_cart', []);
        $cartKey = 'custom_' . time();

        $cart[$cartKey] = [
            "id" => $cartKey,
            "type" => "custom",
            "name" => $request->custom_name,
            "qty" => 1,
            "price" => $totalPrice,
            "image" => null,
            "notes" => $request->custom_notes ?? null,
            "components" => $components
        ];

        Session::put('pos_cart', $cart);
        return redirect()->back()->with('success', 'Custom Buket berhasil ditambahkan');
    }

    public function updateCart(Request $request)
    {
        $cart = Session::get('pos_cart', []);
        if($request->id && $request->qty) {
            if(isset($cart[$request->id])) {
                $cart[$request->id]["qty"] = $request->qty;
                Session::put('pos_cart', $cart);
            }
        }
        return redirect()->back();
    }

    public function removeFromCart(Request $request)
    {
        if($request->id) {
            $cart = Session::get('pos_cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                Session::put('pos_cart', $cart);
            }
        }
        return redirect()->back();
    }

    public function clearCart()
    {
        Session::forget('pos_cart');
        return redirect()->back();
    }

    public function store(Request $request)
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
            'amount_tendered' => 'required|numeric|min:0'
        ]);

        $cart = Session::get('pos_cart', []);
        if(empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang kosong!');
        }

        $baseTotalAmount = 0;
        foreach ($cart as $item) {
            $baseTotalAmount += $item['price'] * $item['qty'];
        }

        $deliveryFee = 0;
        if($request->delivery_method === 'delivery' && $request->delivery_distance) {
            $distanceStr = str_replace(',', '.', $request->delivery_distance);
            $distance = floatval($distanceStr);
            $feePerKm = floatval(\App\Models\Setting::get('delivery_fee_per_km', 3000));
            $minFee = floatval(\App\Models\Setting::get('delivery_min_fee', 15000));
            
            if ($distance <= 1) {
                $deliveryFee = 0;
            } else {
                $roundedDistance = ceil($distance);
                $extraKms = $roundedDistance - 1;
                $deliveryFee = $minFee + (max(0, $extraKms - 1) * $feePerKm);
                
                // Pembulatan ke atas kelipatan 5.000
                if ($deliveryFee > 0) {
                    $deliveryFee = ceil($deliveryFee / 5000) * 5000;
                }
            }
        }

        $totalAmount = $baseTotalAmount + $deliveryFee;

        if ($request->amount_tendered < $totalAmount) {
            return redirect()->back()->with('error', 'Uang yang dibayarkan kurang dari total tagihan akhir!');
        }

        DB::beginTransaction();
        try {
            // Generate Order Number
            $prefix = 'PJL';
            if ($request->scheduled_at) {
                if (\Carbon\Carbon::parse($request->scheduled_at)->isAfter(now()->addHours(3))) {
                    $prefix = 'PES';
                }
            }
            $latestOrder = Order::where('order_number', 'LIKE', $prefix . '%')->orderBy('id', 'desc')->lockForUpdate()->first();
            if ($latestOrder) {
                $lastNumber = intval(substr($latestOrder->order_number, strlen($prefix)));
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }
            $orderNumber = $prefix . $newNumber;

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'delivery_method' => $request->delivery_method,
                'delivery_address' => ($request->map_address ? $request->map_address . ' - Detail: ' : '') . $request->detail_address,
                'delivery_distance' => $request->delivery_distance,
                'delivery_fee' => $deliveryFee,
                'scheduled_at' => $request->scheduled_at,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
                'payment_status' => 'paid',
                'status' => 'processing', 
                'source' => 'offline', 
                'handled_by' => session('pos_florist'),
                'user_id' => auth()->id() ?? null
            ]);

            foreach ($cart as $key => $item) {
                $productId = $item['type'] === 'product' ? $item['id'] : null;
                $finalName = $item['name'];
                if ($item['type'] === 'custom' && !empty($item['notes'])) {
                    $finalName .= " (Catatan: " . $item['notes'] . ")";
                }
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $finalName,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                    'is_rented' => $item['is_rented'] ?? false,
                    'rental_duration' => $item['rental_duration'] ?? null
                ]);

                // Handle Stock Deductions and Components
                if ($item['type'] === 'product') {
                    $product = Product::with('components.material')->find($item['id']);
                    if ($product) {
                        foreach ($product->components as $comp) {
                            $qtyToDeduct = $comp->qty * $item['qty'];
                            $this->deductMaterial($comp->material_id, $qtyToDeduct, $order->order_number);
                            
                            OrderItemComponent::create([
                                'order_item_id' => $orderItem->id,
                                'material_name' => $comp->material->name,
                                'qty' => $qtyToDeduct,
                                'unit_price' => $comp->material->price,
                                'subtotal' => $comp->material->price * $qtyToDeduct
                            ]);
                        }
                    }
                } elseif ($item['type'] === 'material') {
                    $qtyToDeduct = $item['qty'];
                    $this->deductMaterial($item['id'], $qtyToDeduct, $order->order_number);
                    
                    OrderItemComponent::create([
                        'order_item_id' => $orderItem->id,
                        'material_name' => $item['name'],
                        'qty' => $qtyToDeduct,
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $qtyToDeduct
                    ]);
                } elseif ($item['type'] === 'custom') {
                    foreach ($item['components'] as $comp) {
                        $qtyToDeduct = $comp['qty'] * $item['qty'];
                        $this->deductMaterial($comp['material_id'], $qtyToDeduct, $order->order_number);
                        
                        OrderItemComponent::create([
                            'order_item_id' => $orderItem->id,
                            'material_name' => $comp['name'] ?? 'Bahan',
                            'qty' => $qtyToDeduct,
                            'unit_price' => $comp['unit_price'] ?? ($comp['price'] ?? 0),
                            'subtotal' => ($comp['unit_price'] ?? ($comp['price'] ?? 0)) * $qtyToDeduct
                        ]);
                    }
                }
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount_tendered,  
                'status' => 'verified',
                'verified_by' => auth()->id() ?? null,
                'verified_at' => now(),
                'reference_number' => 'POS-' . time()
            ]);

            DB::commit();
            Session::forget('pos_cart');

            // Set session variable for printing popup and return back to POS Main Menu
            return redirect()->route('pos.index')->with('print_order_id', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pos.index')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    private function deductMaterial(int|null $materialId, int $qty, string $orderNumber)
    {
        $material = Material::find($materialId);
        if ($material) {
            $material->decrement('stock', $qty);
            StockMutation::create([
                'material_id' => $materialId,
                'user_id' => auth()->id() ?? null,
                'type' => 'out',
                'qty' => $qty,
                'notes' => "Penjualan via POS - Order: " . $orderNumber
            ]);
        }
    }
}
