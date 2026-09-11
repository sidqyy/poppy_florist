<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItemComponent;
use App\Models\StockMutation;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRevisionController extends Controller
{
    public function editComponents(string $id)
    {
        $order = Order::with(['items.components'])->findOrFail($id);
        $materials = Material::where('is_active', true)->get();

        return view('orders.revision', compact('order', 'materials'));
    }

    public function storeComponent(Request $request, string $id, StockService $stockService)
    {
        $order = Order::with('items')->findOrFail($id);
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $material = Material::findOrFail($request->material_id);

        DB::beginTransaction();
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

                StockMutation::create([
                    'material_id' => $material->id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'qty' => $request->qty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => 'Penambahan komponen (Revisi) untuk pesanan '.$order->order_number,
                ]);
            }

            // 2. Tambahkan ke snapshot order_item_components
            $orderItem = $order->items->first(); // Menggunakan order_item pertama sebagai induk (biasanya custom bucket 1 item)
            if (! $orderItem) {
                throw new \Exception('Pesanan tidak memiliki item untuk ditambahkan komponen.');
            }

            $subtotal = $material->price * $request->qty;

            OrderItemComponent::create([
                'order_item_id' => $orderItem->id,
                'material_id' => $material->id,
                'material_name' => $material->name,
                'qty' => $request->qty,
                'unit_price' => $material->price,
                'subtotal' => $subtotal,
            ]);

            // 3. Recalculate price
            $orderItem->update([
                'price' => $orderItem->price + $subtotal,
                'subtotal' => $orderItem->subtotal + $subtotal,
            ]);
            $order->update([
                'total_amount' => $order->total_amount + $subtotal,
            ]);

            // 4. Log histori
            OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'old_status' => $order->status,
                'new_status' => $order->status,
                'action' => 'revision',
                'notes' => 'Penambahan komponen: '.$request->qty.'x '.$material->name.' ('.($request->notes ?? 'Tidak ada catatan').')',
            ]);

            DB::commit();

            return back()->with('success', 'Komponen berhasil ditambahkan dan tagihan diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Gagal menambah komponen: '.$e->getMessage()]);
        }
    }

    public function deleteComponent(Request $request, string $id, string $componentId, StockService $stockService)
    {
        $order = Order::findOrFail($id);
        $component = OrderItemComponent::findOrFail($componentId);

        DB::beginTransaction();
        try {
            // 1. Kembalikan stok material JIKA order sedang dikerjakan
            $deductedStates = ['processing', 'ready', 'completed'];
            if (in_array($order->status, $deductedStates) && $component->material_id) {
                $material = Material::find($component->material_id);
                if ($material) {
                    $stockBefore = $material->stock;
                    $material->increment('stock', $component->qty);
                    $stockAfter = $material->fresh()->stock;

                    StockMutation::create([
                        'material_id' => $material->id,
                        'user_id' => Auth::id(),
                        'type' => 'in',
                        'qty' => $component->qty,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'notes' => 'Penghapusan komponen (Revisi) dari pesanan '.$order->order_number,
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
                'subtotal' => $orderItem->subtotal - $subtotal,
            ]);
            $order->update([
                'total_amount' => $order->total_amount - $subtotal,
            ]);

            // 4. Log histori
            $notes = $request->input('notes', 'Penghapusan komponen');
            OrderHistory::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'old_status' => $order->status,
                'new_status' => $order->status,
                'action' => 'revision',
                'notes' => 'Penghapusan komponen: '.$qty.'x '.$materialName.' ('.$notes.')',
            ]);

            DB::commit();

            return back()->with('success', 'Komponen berhasil dihapus dan tagihan diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Gagal menghapus komponen: '.$e->getMessage()]);
        }
    }
}
