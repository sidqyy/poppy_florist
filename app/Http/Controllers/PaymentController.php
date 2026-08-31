<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, string $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string'
        ]);

        $imagePath = null;
        if ($request->hasFile('proof_image')) {
            $imagePath = \App\Services\ImageOptimizerService::uploadAndOptimize($request->file('proof_image'), 'payments');
        }

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'proof_image' => $imagePath,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Pembayaran berhasil dicatat. Menunggu verifikasi kasir.');
    }

    public function verify(Request $request, string $paymentId)
    {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        
        if ($payment->status === 'verified') {
            return back()->withErrors(['error' => 'Pembayaran ini sudah diverifikasi sebelumnya.']);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $payment->update([
                'status' => 'verified',
                'verified_at' => now(),
                'user_id' => \Illuminate\Support\Facades\Auth::id() // Update kasir yang memverifikasi
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
                
                \App\Models\OrderHistory::create([
                    'order_id' => $order->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'old_status' => $order->status,
                    'new_status' => $order->status,
                    'action' => 'payment_update',
                    'notes' => 'Status pembayaran otomatis berubah menjadi: ' . strtoupper($newPaymentStatus)
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            
            \App\Services\AuditService::log('Verifikasi Pembayaran', ['status' => 'pending'], ['status' => 'verified', 'amount' => $payment->amount]);

            return back()->with('success', 'Pembayaran sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' BERHASIL diverifikasi!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function uploadProof(Request $request, string $paymentId)
    {
        $payment = \App\Models\Payment::findOrFail($paymentId);
        
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('proof_image')) {
            $imagePath = \App\Services\ImageOptimizerService::uploadAndOptimize($request->file('proof_image'), 'payments');
            $payment->update(['proof_image' => $imagePath]);
            return back()->with('success', 'Bukti pembayaran berhasil diunggah susulan.');
        }

        return back()->withErrors(['error' => 'Gagal mengunggah foto.']);
    }
}