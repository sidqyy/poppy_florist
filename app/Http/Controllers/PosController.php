<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Material;
use App\Models\ArrangementService;
use App\Models\StockMutation;
use App\Models\OrderItemComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'florist_name' => 'required|string'
        ]);

        session(['pos_florist' => $request->florist_name]);

        return redirect()->route('pos.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('pos_florist');
        $request->session()->forget('pos_cart');

        return redirect()->route('pos.login');
    }

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

        $products = $query->paginate(10)->withQueryString();

        $materials = Material::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $data = $this->getCartData();
        $data['categories'] = $categories;
        $data['products'] = $products;
        $data['materials'] = $materials;

        return view('pos.catalog', $data);
    }

    public function materials(string $type)
    {
        $materials = Material::where('type', $type)
            ->where('is_active', true)
            ->get();

        $titles = [
            'flower_fresh' => 'Bunga Segar Eceran',
            'flower_artificial' => 'Bunga Artificial Eceran',
            'wrapping' => 'Kertas Wrapping',
            'ribbon' => 'Pita (Ribbon)',
            'doll' => 'Boneka',
            'greeting_card' => 'Kartu Ucapan',
            'accessory' => 'Aksesoris',
            'packaging' => 'Packaging',
        ];

        $title = $titles[$type] ?? 'Bahan Eceran';

        $data = $this->getCartData();
        $data['materials'] = $materials;
        $data['title'] = $title;
        $data['type'] = $type;

        return view('pos.materials', $data);
    }

    public function custom()
    {
        $materials = Material::where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        $arrangementServices = ArrangementService::where('is_active', true)
            ->orderBy('min_item')
            ->get();

        $data = $this->getCartData();
        $data['groupedMaterials'] = $materials;
        $data['arrangementServices'] = $arrangementServices;

        return view('pos.custom', $data);
    }

    private function getMaterialStemPrice(Material $material)
    {
        return ($material->price_stem ?? 0) > 0
            ? $material->price_stem
            : $material->price;
    }

    private function getMaterialArrangementPrice(Material $material)
    {
        return ($material->price_arrangement ?? 0) > 0
            ? $material->price_arrangement
            : $material->price;
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

        if (isset($cart[$cartKey])) {
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

public function addVariantProductToCart(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'size_id' => 'required|exists:product_sizes,id',
        'variant_id' => 'required|exists:product_variants,id',
        'materials' => 'nullable|array',
        'materials.*.material_id' => 'nullable|exists:materials,id',
        'materials.*.qty' => 'nullable|integer|min:1',
        'is_rented' => 'nullable|in:0,1',
        'rental_duration' => 'nullable|integer|min:1',
    ]);

    $product = Product::findOrFail($request->product_id);

    $size = ProductSize::where('product_id', $product->id)
        ->where('id', $request->size_id)
        ->firstOrFail();

    $variant = ProductVariant::where('product_size_id', $size->id)
        ->where('id', $request->variant_id)
        ->firstOrFail();

    $isRented = false;
    $rentalDuration = null;
    $finalPrice = $variant->price;

    if (
        $product->is_rentable &&
        $request->has('is_rented') &&
        $request->is_rented == '1'
    ) {
        $isRented = true;
        $rentalDuration = max(1, intval($request->rental_duration));
        $finalPrice = ($product->rental_price_per_day ?? 0) * $rentalDuration;

        if ($finalPrice <= 0) {
            return back()->with('error', 'Harga sewa produk belum diatur.');
        }
    }

    $components = [];

    $needChooseComponents = $product->has_flexible_components == 1;

    if ($needChooseComponents) {
        if (!$request->has('materials') || !is_array($request->materials)) {
            return back()->with('error', 'Produk ini wajib memilih komponen terlebih dahulu.');
        }

        foreach ($request->materials as $item) {
            if (empty($item['material_id']) || empty($item['qty'])) {
                continue;
            }

            $material = Material::find($item['material_id']);

            if (!$material) {
                continue;
            }

            $price = $this->getMaterialArrangementPrice($material);

            $components[] = [
                'material_id' => $material->id,
                'name' => $material->name,
                'qty' => intval($item['qty']),
                'price' => $price,
                'unit_price' => $price
            ];
        }

        if (count($components) === 0) {
            return back()->with('error', 'Pilih minimal satu komponen untuk produk ini.');
        }
    }

    $cart = Session::get('pos_cart', []);

    $cartKey =
        'variant_' .
        $product->id . '_' .
        $size->id . '_' .
        $variant->id . '_' .
        ($isRented ? 'rent_' . $rentalDuration . '_' : '') .
        time();

    $variantImage = $variant->image
        ? $variant->image
        : ($size->image
            ? $size->image
            : $product->image);

    $cart[$cartKey] = [
        "id" => $product->id,
        "type" => "product_variant",
        "name" => $product->name . ' - ' . $size->size_name . ' - ' . $variant->variant_name,
        "qty" => 1,
        "price" => $finalPrice,
        "image" => $variantImage,

        "is_rented" => $isRented,
        "rental_duration" => $rentalDuration,

        "product_size_id" => $size->id,
        "product_variant_id" => $variant->id,
        "size_name" => $size->size_name,
        "variant_name" => $variant->variant_name,
        "components" => $components
    ];

    Session::put('pos_cart', $cart);

    return back()->with('success', 'Produk varian berhasil ditambahkan ke keranjang.');
}

    public function addMaterialToCart(Request $request)
    {
        $material = Material::findOrFail($request->material_id);
        $cart = Session::get('pos_cart', []);
        $cartKey = 'mat_' . $material->id;
        $price = $this->getMaterialStemPrice($material);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['qty']++;
        } else {
            $cart[$cartKey] = [
                "id" => $material->id,
                "type" => "material",
                "name" => $material->name . ' (Eceran)',
                "qty" => 1,
                "price" => $price,
                "image" => $material->image
            ];
        }

        Session::put('pos_cart', $cart);

        return redirect()->back()->with('success', 'Bahan berhasil ditambahkan ke keranjang');
    }

    public function addMultipleMaterialsToCart(Request $request)
    {
        $request->validate([
            'materials' => 'required|array'
        ]);

        $cart = Session::get('pos_cart', []);
        $addedCount = 0;

        foreach ($request->materials as $matId => $qty) {
            $qty = intval($qty);

            if ($qty > 0) {
                $material = Material::find($matId);

                if ($material) {
                    $cartKey = 'mat_' . $material->id;
                    $price = $this->getMaterialStemPrice($material);

                    if (isset($cart[$cartKey])) {
                        $cart[$cartKey]['qty'] += $qty;
                    } else {
                        $cart[$cartKey] = [
                            "id" => $material->id,
                            "type" => "material",
                            "name" => $material->name . ' (Eceran)',
                            "qty" => $qty,
                            "price" => $price,
                            "image" => $material->image
                        ];
                    }

                    $addedCount += $qty;
                }
            }
        }

        if ($addedCount > 0) {
            Session::put('pos_cart', $cart);

            return redirect()->back()->with('success', $addedCount . ' barang eceran berhasil ditambahkan ke keranjang!');
        }

        return redirect()->back()->with('error', 'Pilih minimal satu barang dengan jumlah lebih dari 0!');
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
        'extra_items.*.qty' => 'required|integer|min:1',
        'is_premium_service' => 'nullable|boolean',
    ]);

    $components = [];
    $totalPrice = 0;

    // Ini khusus untuk menghitung jasa rangkai.
    // Hanya flower_fresh yang dihitung ke min/max item jasa.
    $freshFlowerCount = 0;

    foreach ($request->materials as $matId => $qty) {
        $qty = intval($qty);

        if ($qty > 0) {
            $material = Material::find($matId);

            if ($material) {
                if ($material->type === 'service') {
                    continue;
                }

                $price = $this->getMaterialArrangementPrice($material);
                $subtotal = $price * $qty;

                $totalPrice += $subtotal;

                if ($material->type === 'flower_fresh') {
                    $freshFlowerCount += $qty;
                }

                $components[] = [
                    'material_id' => $material->id,
                    'name' => $material->name,
                    'qty' => $qty,
                    'price' => $price,
                    'unit_price' => $price,
                ];
            }
        }
    }

    if ($request->extra_items) {
        foreach ($request->extra_items as $extra) {
            $qty = intval($extra['qty']);
            $price = floatval($extra['price']);

            if ($qty > 0) {
                $subtotal = $price * $qty;

                $totalPrice += $subtotal;

                $components[] = [
                    'material_id' => null,
                    'name' => $extra['name'],
                    'qty' => $qty,
                    'price' => $price,
                    'unit_price' => $price,
                ];
            }
        }
    }

    if (empty($components)) {
        return redirect()->back()->with('error', 'Pilih minimal satu bahan untuk custom buket');
    }

    $isPremium = $request->has('is_premium_service');

    if ($freshFlowerCount > 0) {
        $service = ArrangementService::where('is_active', true)
            ->where('is_premium', $isPremium)
            ->where('min_item', '<=', $freshFlowerCount)
            ->where(function ($query) use ($freshFlowerCount) {
                $query->where('max_item', '>=', $freshFlowerCount)
                    ->orWhereNull('max_item');
            })
            ->orderBy('min_item')
            ->first();

        if ($service) {
            $servicePrice = (float) $service->price;
            $totalPrice += $servicePrice;

            $components[] = [
                'material_id' => null,
                'name' => 'JS ' . $service->name,
                'qty' => 1,
                'price' => $servicePrice,
                'unit_price' => $servicePrice,
            ];
        }
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
        "components" => $components,
    ];

    Session::put('pos_cart', $cart);

    return redirect()->back()->with('success', 'Custom Buket berhasil ditambahkan');
}

    public function updateCart(Request $request)
    {
        $cart = Session::get('pos_cart', []);

        if ($request->id && $request->qty) {
            if (isset($cart[$request->id])) {
                $cart[$request->id]["qty"] = $request->qty;
                Session::put('pos_cart', $cart);
            }
        }

        return redirect()->back();
    }

    public function removeFromCart(Request $request)
    {
        if ($request->id) {
            $cart = Session::get('pos_cart', []);

            if (isset($cart[$request->id])) {
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

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang kosong!');
        }

        $baseTotalAmount = 0;

        foreach ($cart as $item) {
            $baseTotalAmount += $item['price'] * $item['qty'];
        }

        $deliveryFee = 0;

        if ($request->delivery_method === 'delivery' && $request->delivery_distance) {
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
            $prefix = 'PJL';

            if ($request->scheduled_at) {
                if (\Carbon\Carbon::parse($request->scheduled_at)->isAfter(now()->addHours(3))) {
                    $prefix = 'PES';
                }
            }

            $latestOrder = Order::where('order_number', 'like', $prefix . '%')
                ->select('order_number')
                ->orderByRaw("
                    CAST(
                        REGEXP_REPLACE(order_number, '[^0-9]', '')
                        AS UNSIGNED
                    ) DESC
                ")
                ->lockForUpdate()
                ->first();

            if ($latestOrder) {
                preg_match('/(\d+)$/', $latestOrder->order_number, $matches);

                $lastNumber = isset($matches[1])
                    ? (int) $matches[1]
                    : 0;

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
                'user_id' => Auth::id() ?? null
            ]);

            foreach ($cart as $key => $item) {
                $productId = in_array($item['type'], ['product', 'product_variant']) ? $item['id'] : null;
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

                if ($item['type'] === 'product') {
                    $product = Product::with('components.material')->find($item['id']);

                    if ($product) {
                        foreach ($product->components as $comp) {
                            $qtyToDeduct = $comp->qty * $item['qty'];
                            $this->deductMaterial($comp->material_id, $qtyToDeduct, $order->order_number);

                            $componentPrice = $comp->material
                                ? $this->getMaterialArrangementPrice($comp->material)
                                : 0;

                            OrderItemComponent::create([
                                'order_item_id' => $orderItem->id,
                                'material_name' => $comp->material ? $comp->material->name : 'Bahan',
                                'qty' => $qtyToDeduct,
                                'unit_price' => $componentPrice,
                                'subtotal' => $componentPrice * $qtyToDeduct
                            ]);
                        }
                        
                        // Auto-hide if it's an artificial flower
                        if ($product->categories()->where('slug', 'bunga-artificial')->exists()) {
                            $product->is_active = false;
                            $product->save();
                        }
                    }
                } elseif ($item['type'] === 'product_variant') {
                    foreach ($item['components'] ?? [] as $comp) {
                        $qtyToDeduct = $comp['qty'] * $item['qty'];

                        if (!empty($comp['material_id'])) {
                            $this->deductMaterial($comp['material_id'], $qtyToDeduct, $order->order_number);
                        }

                        OrderItemComponent::create([
                            'order_item_id' => $orderItem->id,
                            'material_name' => $comp['name'] ?? 'Bahan',
                            'qty' => $qtyToDeduct,
                            'unit_price' => $comp['unit_price'] ?? ($comp['price'] ?? 0),
                            'subtotal' => ($comp['unit_price'] ?? ($comp['price'] ?? 0)) * $qtyToDeduct
                        ]);
                    }
                    
                    $product = Product::find($item['id']);
                    if ($product && $product->categories()->where('slug', 'bunga-artificial')->exists()) {
                        $product->is_active = false;
                        $product->save();
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

                        if (!empty($comp['material_id'])) {
                            $this->deductMaterial($comp['material_id'], $qtyToDeduct, $order->order_number);
                        }

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
                'verified_by' => Auth::id() ?? null,
                'verified_at' => now(),
                'reference_number' => 'POS-' . time()
            ]);

            DB::commit();

            Session::forget('pos_cart');

            return redirect()->route('pos.index')->with('print_order_id', $order->id);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('pos.index')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    private function deductMaterial(int|null $materialId, int $qty, string $orderNumber)
    {
        if (!$materialId) {
            return;
        }

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
                'user_id' => Auth::id() ?? null,
                'type' => 'out',
                'qty' => $qtyToDeduct,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => "Penjualan via POS - Order: " . $orderNumber
            ]);
        }
    }
}