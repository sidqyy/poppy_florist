<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Payment;
use App\Services\AuditService;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, string $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            $imagePath = ImageOptimizerService::uploadAndOptimize($request->file('proof_image'), 'payments');
        }

        Payment::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'proof_image' => $imagePath,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Pembayaran berhasil dicatat. Menunggu verifikasi kasir.');
    }

    public function verify(Request $request, string $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if ($payment->status === 'verified') {
            return back()->withErrors(['error' => 'Pembayaran ini sudah diverifikasi sebelumnya.']);
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 'verified',
                'verified_at' => now(),
                'user_id' => Auth::id(), // Update kasir yang memverifikasi
            ]);

            // Cek status pesanan keseluruhan
            $order = $payment->order;

            // Reload order to recalculate total_dibayar
            $order->load('payments');

            $newPaymentStatus = 'dp';
            if ($order->sisa_tagihan <= 0) {
                $newPaymentStatus = 'paid';
            }

            if ($order->payment_status !== $newPaymentStatus) {
                $order->update(['payment_status' => $newPaymentStatus]);

                OrderHistory::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'old_status' => $order->status,
                    'new_status' => $order->status,
                    'action' => 'payment_update',
                    'notes' => 'Status pembayaran otomatis berubah menjadi: '.strtoupper($newPaymentStatus),
                ]);
            }

            DB::commit();

            AuditService::log('Verifikasi Pembayaran', ['status' => 'pending'], ['status' => 'verified', 'amount' => $payment->amount]);

            return back()->with('success', 'Pembayaran sebesar Rp '.number_format($payment->amount, 0, ',', '.').' BERHASIL diverifikasi!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Terjadi kesalahan: '.$e->getMessage()]);
        }
    }

    public function uploadProof(Request $request, string $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('proof_image')) {
            $imagePath = ImageOptimizerService::uploadAndOptimize($request->file('proof_image'), 'payments');
            $payment->update(['proof_image' => $imagePath]);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah susulan.');
        }

        return back()->withErrors(['error' => 'Gagal mengunggah foto.']);
    }
}
