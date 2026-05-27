<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('local');
        $files = $disk->files('backups');
        
        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => $this->formatSizeUnits($disk->size($file)),
                'last_modified' => \Carbon\Carbon::createFromTimestamp($disk->lastModified($file)),
                'path' => $file
            ];
        }

        // Urutkan dari yang terbaru
        usort($backups, function ($a, $b) {
            return $b['last_modified']->timestamp - $a['last_modified']->timestamp;
        });

        return view('admin.backups.index', compact('backups'));
    }

    public function runBackup()
    {
        try {
            Artisan::call('db:backup');
            
            $output = Artisan::output();
            
            \App\Services\AuditService::log('Membuat Backup Database Manual');

            return back()->with('success', 'Backup berhasil dilakukan. ' . $output);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal melakukan backup: ' . $e->getMessage()]);
        }
    }

    public function download($fileName)
    {
        $path = 'backups/' . $fileName;
        if (!Storage::disk('local')->exists($path)) {
            return back()->withErrors(['error' => 'File backup tidak ditemukan.']);
        }
        
        \App\Services\AuditService::log('Mengunduh File Backup', null, ['file' => $fileName]);

        return Storage::disk('local')->download($path);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimetypes:text/plain,application/sql,text/x-sql|max:50000'
        ]);

        try {
            $file = $request->file('backup_file');
            $sqlContent = file_get_contents($file->getRealPath());

            // Sangat berbahaya: Ini akan mengeksekusi raw query
            \Illuminate\Support\Facades\DB::unprepared($sqlContent);
            
            \App\Services\AuditService::log('Melakukan Restore Database', null, ['file' => $file->getClientOriginalName()]);

            return back()->with('success', 'Database berhasil direstore dari file ' . $file->getClientOriginalName());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal melakukan restore: ' . $e->getMessage()]);
        }
    }

    public function destroy($fileName)
    {
        $path = 'backups/' . $fileName;
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            \App\Services\AuditService::log('Menghapus File Backup', null, ['file' => $fileName]);
            return back()->with('success', 'File backup berhasil dihapus.');
        }

        return back()->withErrors(['error' => 'File backup tidak ditemukan.']);
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
