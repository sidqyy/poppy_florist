<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log an activity
     *
     * @param string $action Deskripsi aktivitas (contoh: "Membuat Pesanan")
     * @param array|null $oldValues Data sebelum diubah
     * @param array|null $newValues Data sesudah diubah atau data baru
     * @param int|null $userId ID user yang melakukan aksi (default: Auth user)
     */
    public static function log(string $action, $oldValues = null, $newValues = null, $userId = null)
    {
        $request = request();

        AuditLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
