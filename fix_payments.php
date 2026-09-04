<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Payment;

echo "--- MEMULAI UPDATE STATUS PEMBAYARAN PESANAN LAMA ---\n";

$orders = Order::where('payment_status', 'paid')->get();

$countQris = 0;
$countTf = 0;

foreach ($orders as $order) {
    $payment = Payment::where('order_id', $order->id)->latest()->first();
    if ($payment && !empty($payment->payment_method)) {
        $method = strtolower($payment->payment_method);
        
        if (str_contains($method, 'qris')) {
            $order->payment_status = 'paid_qris';
            $order->save();
            $countQris++;
        } elseif (str_contains($method, 'tf') || str_contains($method, 'transfer') || str_contains($method, 'online')) {
            $order->payment_status = 'paid_tf';
            $order->save();
            $countTf++;
        }
    }
}

echo "SELESAI!\n";
echo "- Total diperbarui ke LUNAS QRIS: {$countQris} pesanan\n";
echo "- Total diperbarui ke LUNAS TF: {$countTf} pesanan\n";
