<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan backup database MySQL ke dalam file .sql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = 'backup_poppy_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        
        // Buat folder jika belum ada
        if (!Storage::disk('local')->exists('backups')) {
            Storage::disk('local')->makeDirectory('backups');
        }

        $storagePath = storage_path('app/backups/' . $filename);

        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbUsername = env('DB_USERNAME', 'root');
        $dbPassword = env('DB_PASSWORD', '');
        $dbDatabase = env('DB_DATABASE', 'poppy_florist');

        // Konstruksi command mysqldump
        $passwordString = empty($dbPassword) ? '' : "-p\"{$dbPassword}\"";
        
        // Command for windows (if mysqldump is in PATH)
        $command = "mysqldump -h {$dbHost} -P {$dbPort} -u {$dbUsername} {$passwordString} {$dbDatabase} > \"{$storagePath}\" 2>&1";

        $output = [];
        $returnVar = 0;
        
        $this->info("Menjalankan backup...");
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Gagal melakukan backup. Pastikan 'mysqldump' tersedia di Environment Variables (PATH).");
            \Log::error("Backup gagal: " . implode("\n", $output));
            return Command::FAILURE;
        }

        $this->info("Backup berhasil disimpan ke: " . $storagePath);
        
        // Opsional: Hapus backup yang lebih lama dari 7 hari
        $this->cleanOldBackups();
        
        return Command::SUCCESS;
    }

    private function cleanOldBackups()
    {
        $files = Storage::disk('local')->files('backups');
        $now = Carbon::now();

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file));
            
            // Hapus jika umurnya lebih dari 7 hari
            if ($now->diffInDays($lastModified) > 7) {
                Storage::disk('local')->delete($file);
                $this->info("Backup usang dihapus: " . $file);
            }
        }
    }
}
