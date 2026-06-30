<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:truncate-transactions {--force : Skip the confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate Order and Payment tables, resetting auto-increment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  Peringatan: Operasi ini akan MENGHAPUS SEMUA data di tabel Orders dan Payments!');
            if (!$this->confirm('Apakah Anda yakin ingin melanjutkan?')) {
                $this->info('Operasi dibatalkan.');
                return;
            }
        }

        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Truncate tables
            DB::table('order_item_components')->truncate();
            $this->info('✓ Tabel order_item_components dibersihkan');

            DB::table('order_items')->truncate();
            $this->info('✓ Tabel order_items dibersihkan');

            DB::table('order_images')->truncate();
            $this->info('✓ Tabel order_images dibersihkan');

            DB::table('order_histories')->truncate();
            $this->info('✓ Tabel order_histories dibersihkan');

            DB::table('payments')->truncate();
            $this->info('✓ Tabel payments dibersihkan');

            DB::table('orders')->truncate();
            $this->info('✓ Tabel orders dibersihkan');

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->info('✅ Semua data transaksi telah dihapus dan auto-increment direset!');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
