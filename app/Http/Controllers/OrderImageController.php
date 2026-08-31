<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderImageController extends Controller
{
    public function store(Request $request, string $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string'
        ]);
        $imagePath = \App\Services\ImageOptimizerService::uploadAndOptimize($request->file('image'), 'order_results');

        \App\Models\OrderImage::create([
            'order_id' => $order->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'image_path' => $imagePath,
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Foto hasil berhasil diunggah.');
    }

    public function destroy(string $id)
    {
        $image = \App\Models\OrderImage::findOrFail($id);
        
        // Hapus file fisik
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}