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
            // 1. Kurangi stok material baru JIKA order sedang dikerjakan
            $deductedStates = ['processing', 'ready', 'completed'];
            if (in_array($order->status, $deductedStates)) {
                if ($material->stock < $request->qty) {
                    throw new \Exception("Stok tidak mencukupi. Tersedia: {$material->stock}");
                }
                $stockBefore = $material->stock;
                $material->decrement('stock', $request->qty);
                $stockAfter = $material->fresh()->stock;
                
                \App\Models\StockMutation::create([
                    'material_id' => $material->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'type' => 'out',
                    'qty' => $request->qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => 'Penambahan komponen (Revisi) untuk pesanan ' . $order->order_number
                ]);
            }

            // 2. Tambahkan ke snapshot order_item_components
            $orderItem = $order->items->first(); // Menggunakan order_item pertama sebagai induk (biasanya custom bucket 1 item)
            if (!$orderItem) {
                throw new \Exception('Pesanan tidak memiliki item untuk ditambahkan komponen.');
            }

            $subtotal = $material->price * $request->qty;

            \App\Models\OrderItemComponent::create([
                'order_item_id' => $orderItem->id,
                'material_id' => $material->id,
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
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
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
            // 1. Kembalikan stok material JIKA order sedang dikerjakan
            $deductedStates = ['processing', 'ready', 'completed'];
            if (in_array($order->status, $deductedStates) && $component->material_id) {
                $material = \App\Models\Material::find($component->material_id);
                if ($material) {
                    $stockBefore = $material->stock;
                    $material->increment('stock', $component->qty);
                    $stockAfter = $material->fresh()->stock;
                    
                    \App\Models\StockMutation::create([
                        'material_id' => $material->id,
                        'user_id' => \Illuminate\Support\Facades\Auth::id(),
                        'type' => 'in',
                        'qty' => $component->qty,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => 'Penghapusan komponen (Revisi) dari pesanan ' . $order->order_number
                    ]);
                }
            }

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
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
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