<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\StockMutation;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\DB;

class AutoCompleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark orders as completed if their scheduled_at time has passed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find orders that are pending, processing, or ready and have passed their scheduled_at time
        $orders = Order::with('items.components.material')
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->where('is_printed', true)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders to auto-complete.');
            return;
        }

        $completedCount = 0;
        $skippedCount = 0;

        foreach ($orders as $order) {
            DB::beginTransaction();

            try {
                $oldStatus = $order->status;
                
                // If it was pending, we must deduct stock
                if ($oldStatus === 'pending') {
                    // 1. Check stock sufficiency
                    $stockSufficient = true;
                    foreach ($order->items as $item) {
                        foreach ($item->components as $component) {
                            if ($component->material && $component->material->stock < $component->qty) {
                                $stockSufficient = false;
                                break 2;
                            }
                        }
                    }

                    if (!$stockSufficient) {
                        DB::rollBack();
                        $this->warn("Skipped Order {$order->order_number}: Insufficient stock for auto-completion.");
                        $skippedCount++;
                        continue;
                    }

                    // 2. Deduct stock
                    foreach ($order->items as $item) {
                        foreach ($item->components as $component) {
                            if ($component->material) {
                                $material = $component->material;
                                $stockBefore = $material->stock;

                                $material->decrement('stock', $component->qty);
                                $stockAfter = $material->fresh()->stock;

                                StockMutation::create([
                                    'material_id' => $material->id,
                                    'user_id' => null, // System (null allowed if column is nullable)
                                    'type' => 'out',
                                    'qty' => $component->qty,
                                    'stock_before' => $stockBefore,
                                    'stock_after' => $stockAfter,
                                    'notes' => 'Otomatis digunakan untuk pesanan ' . $order->order_number . ' (Auto-Complete)',
                                ]);
                            }
                        }
                    }
                }

                // Update Order
                $updateData = [
                    'status' => 'completed',
                    'completed_at' => now(),
                ];

                if ($oldStatus === 'pending' && !$order->started_at) {
                    $updateData['started_at'] = now();
                } elseif ($oldStatus === 'processing' && !$order->completed_at) {
                    $updateData['completed_at'] = now();
                }

                $order->update($updateData);

                // Add History
                OrderHistory::create([
                    'order_id' => $order->id,
                    'user_id' => null, // System
                    'old_status' => $oldStatus,
                    'new_status' => 'completed',
                    'action' => 'status_update',
                    'notes' => 'Status otomatis diselesaikan oleh Sistem (Auto-Complete) karena melewati batas waktu.',
                ]);

                DB::commit();
                $completedCount++;
                $this->info("Successfully auto-completed Order {$order->order_number}.");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to auto-complete Order {$order->order_number}: " . $e->getMessage());
                $skippedCount++;
            }
        }

        $this->info("Auto-Complete finished. Completed: {$completedCount}, Skipped: {$skippedCount}.");
    }
}
