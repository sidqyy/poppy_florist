<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Support\Str;

class KioskController extends Controller
{
    public function welcome()
    {
        return view('kiosk.welcome');
    }

    public function catalog()
    {
        $products = Product::where('is_available', true)->get();
        $cart = session()->get('kiosk_cart', []);
        
        $totalItems = 0;
        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalItems += $item['qty'];
            $totalPrice += $item['price'] * $item['qty'];
        }

        return view('kiosk.catalog', compact('products', 'cart', 'totalItems', 'totalPrice'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('kiosk_cart', []);

        if(isset($cart[$product->id])) {
            $cart[$product->id]['qty']++;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "qty" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('kiosk_cart', $cart);
        return redirect()->back();
    }

    public function updateCart(Request $request)
    {
        $cart = session()->get('kiosk_cart', []);
        if($request->id && $request->qty) {
            $cart[$request->id]["qty"] = $request->qty;
            session()->put('kiosk_cart', $cart);
        }
        return redirect()->back();
    }

    public function removeFromCart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('kiosk_cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('kiosk_cart', $cart);
            }
        }
        return redirect()->back();
    }

    public function checkout()
    {
        $cart = session()->get('kiosk_cart', []);
        if(empty($cart)) {
            return redirect()->route('kiosk.catalog');
        }

        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['qty'];
        }

        return view('kiosk.checkout', compact('cart', 'totalPrice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $cart = session()->get('kiosk_cart', []);
        if(empty($cart)) {
            return redirect()->route('kiosk.catalog');
        }

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['qty'];
        }

        // Generate Order Number
        $prefix = 'ORD-';
        $latestOrder = Order::where('order_number', 'LIKE', $prefix . '%')->orderBy('id', 'desc')->first();
        if ($latestOrder) {
            $lastNumber = intval(substr($latestOrder->order_number, 4));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }
        $orderNumber = $prefix . $newNumber;

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'total_amount' => $totalAmount,
            'status' => 'pending_payment',
            'delivery_method' => 'pickup', // Kiosk orders are usually picked up
            'type' => 'offline',
            'source' => 'kiosk'
        ]);

        foreach ($cart as $id => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'product_name' => $item['name'],
                'price' => $item['price'],
                'qty' => $item['qty'],
                'subtotal' => $item['price'] * $item['qty']
            ]);
        }

        // Clear cart
        session()->forget('kiosk_cart');

        return redirect()->route('kiosk.success', ['order' => $order->id]);
    }

    public function success(Order $order)
    {
        return view('kiosk.success', compact('order'));
    }
}
