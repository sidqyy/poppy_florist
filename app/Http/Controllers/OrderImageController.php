<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderImage;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderImageController extends Controller
{
    public function store(Request $request, string $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);
        $imagePath = ImageOptimizerService::uploadAndOptimize($request->file('image'), 'order_results');

        OrderImage::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'image_path' => $imagePath,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Foto hasil berhasil diunggah.');
    }

    public function destroy(string $id)
    {
        $image = OrderImage::findOrFail($id);

        // Hapus file fisik
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
