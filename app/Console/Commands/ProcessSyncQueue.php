<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SyncLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessSyncQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memproses antrian sinkronisasi (Cloud/API Eksternal) saat internet tersedia.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Cek Koneksi Internet (Ping Google DNS)
        if (!$this->isOnline()) {
            $this->warn('Internet terputus. Sinkronisasi ditunda.');
            return Command::SUCCESS;
        }

        $this->info('Internet tersedia. Memeriksa antrian sinkronisasi...');

        // 2. Ambil antrian pending (Maks 50 per batch untuk efisiensi)
        $pendingLogs = SyncLog::where('status', 'pending')
                              ->where('retry_count', '<', 5)
                              ->limit(50)
                              ->get();

        if ($pendingLogs->isEmpty()) {
            $this->info('Tidak ada antrian sinkronisasi.');
            return Command::SUCCESS;
        }

        foreach ($pendingLogs as $log) {
            $this->info("Memproses log ID {$log->id} tipe: {$log->type}");

            try {
                // Simulasi pengiriman berdasarkan tipe
                if ($log->type === 'whatsapp') {
                    $this->processWhatsApp($log->payload);
                } elseif ($log->type === 'cloud_sync') {
                    $this->processCloudSync($log->payload);
                } else {
                    throw new \Exception("Tipe sinkronisasi tidak dikenal: {$log->type}");
                }

                $log->update(['status' => 'success']);
                $this->info("Berhasil memproses log ID {$log->id}");

            } catch (\Exception $e) {
                $log->update([
                    'status' => 'failed',
                    'retry_count' => $log->retry_count + 1,
                    'error_message' => $e->getMessage()
                ]);
                $this->error("Gagal memproses log ID {$log->id}: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }

    private function isOnline()
    {
        $connected = @fsockopen("www.google.com", 80); 
        if ($connected){
            fclose($connected);
            return true;
        }
        return false;
    }

    private function processWhatsApp($payload)
    {
        // Dummy simulasi API WhatsApp Gateway
        // Http::post('https://api.whatsapp-provider.com/send', $payload);
        
        // Simulasikan delay API
        usleep(500000); 
        return true;
    }

    private function processCloudSync($payload)
    {
        // Dummy simulasi Push ke Server Pusat
        // Http::withToken('xyz')->post('https://hq.poppyflorist.com/api/sync', $payload);
        return true;
    }
}
