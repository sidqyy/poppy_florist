<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderRevisionController extends Controller
{
    public function editComponents(string $id)
    {
        $order = \App\Models\Order::with(['items.components'])->findOrFail($id);
        $materials = \App\Models\Material::where('is_active', true)->get();
        return view('orders.revision', compact('order', 'materials'));
    }

    public function storeComponent(Request $request, string $id, \App\Services\StockService $stockService)
    {
        $order = \App\Models\Order::with('items')->findOrFail($id);
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $material = \App\Models\Material::findOrFail($request->material_id);
        
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Kurangi stok material baru
            $stockService->deductStockForOrderComponent($material->id, $request->qty, auth()->id());

            // 2. Tambahkan ke snapshot order_item_components
            $orderItem = $order->items->first(); // Menggunakan order_item pertama sebagai induk (biasanya custom bucket 1 item)
            if (!$orderItem) {
                throw new \Exception('Pesanan tidak memiliki item untuk ditambahkan komponen.');
            }

            $subtotal = $material->price * $request->qty;

            \App\Models\OrderItemComponent::create([
                'order_item_id' => $orderItem->id,
                'material_name' => $material->name,
                'qty' => $request->qty,
                'unit_price' => $material->price,
                'subtotal' => $subtotal
            ]);

            // 3. Recalculate price
            $orderItem->update([
                'price' => $orderItem->price + $subtotal,
                'subtotal' => $orderItem->subtotal + $subtotal
            ]);
            $order->update([
                'total_amount' => $order->total_amount + $subtotal
            ]);

            // 4. Log histori
            \App\Models\OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'old_status' => $order->status,
                'new_status' => $order->status,
                'action' => 'revision',
                'notes' => 'Penambahan komponen: ' . $request->qty . 'x ' . $material->name . ' (' . ($request->notes ?? 'Tidak ada catatan') . ')'
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Komponen berhasil ditambahkan dan tagihan diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menambah komponen: ' . $e->getMessage()]);
        }
    }

    public function deleteComponent(Request $request, string $id, string $componentId, \App\Services\StockService $stockService)
    {
        $order = \App\Models\Order::findOrFail($id);
        $component = \App\Models\OrderItemComponent::findOrFail($componentId);
        
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Kembalikan stok material
            $stockService->returnStockForOrderComponent($component->material_name, $component->qty, auth()->id());

            $subtotal = $component->subtotal;
            $materialName = $component->material_name;
            $qty = $component->qty;

            // 2. Hapus komponen
            $orderItem = $component->orderItem;
            $component->delete();

            // 3. Recalculate price
            $orderItem->update([
                'price' => $orderItem->price - $subtotal,
                'subtotal' => $orderItem->subtotal - $subtotal
            ]);
            $order->update([
                'total_amount' => $order->total_amount - $subtotal
            ]);

            // 4. Log histori
            $notes = $request->input('notes', 'Penghapusan komponen');
            \App\Models\OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'old_status' => $order->status,
                'new_status' => $order->status,
                'action' => 'revision',
                'notes' => 'Penghapusan komponen: ' . $qty . 'x ' . $materialName . ' (' . $notes . ')'
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Komponen berhasil dihapus dan tagihan diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus komponen: ' . $e->getMessage()]);
        }
    }
}