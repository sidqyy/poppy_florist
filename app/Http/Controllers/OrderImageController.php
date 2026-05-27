<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderImageController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string'
        ]);

        $imagePath = $request->file('image')->store('order_results', 'public');

        \App\Models\OrderImage::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'image_path' => $imagePath,
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Foto hasil berhasil diunggah.');
    }

    public function destroy($id)
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