<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Material;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use Exception;

class StockService
{
    /**
     * Mengurangi stok bahan baku berdasarkan komponen sebuah produk.
     * Fungsi ini disiapkan untuk dipanggil dari modul Pemesanan (Tahap 5).
     *
     * @param Product $product
     * @param int $productQty Jumlah produk yang dipesan
     * @param int $userId ID Kasir/Florist
     * @param string $notes Catatan mutasi
     * @return bool
     * @throws Exception
     */
    public function reduceStockForProduct(Product $product, int $productQty, int $userId, string $notes = 'Digunakan untuk pesanan')
    {
        DB::beginTransaction();
        try {
            foreach ($product->components as $component) {
                $material = $component->material;
                $totalQtyNeeded = $component->qty * $productQty;

                if ($material->stock < $totalQtyNeeded) {
                    throw new Exception("Stok tidak mencukupi untuk bahan: {$material->name}. Butuh: {$totalQtyNeeded}, Tersedia: {$material->stock}");
                }

                $stockBefore = $material->stock;

                // Kurangi stok di master
                $material->decrement('stock', $totalQtyNeeded);

                $stockAfter = $material->fresh()->stock;

                // Catat di mutasi
                StockMutation::create([
                    'material_id' => $material->id,
                    'user_id' => $userId,
                    'type' => 'out',
                    'qty' => $totalQtyNeeded,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'notes' => $notes
                ]);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mencatat stok masuk (Restock)
     */
    public function addStock(Material $material, int $qty, int $userId, string $notes = 'Restock')
    {
        DB::beginTransaction();
        try {
            $stockBefore = $material->stock;

            $material->increment('stock', $qty);

            $stockAfter = $material->fresh()->stock;

            $expiresAt = null;
            if ($material->type === 'flower_fresh' && $material->freshness_days) {
                $expiresAt = now()->addDays($material->freshness_days);
            }

            StockMutation::create([
                'material_id' => $material->id,
                'user_id' => $userId,
                'type' => 'in',
                'qty' => $qty,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => $notes,
                'expires_at' => $expiresAt
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
