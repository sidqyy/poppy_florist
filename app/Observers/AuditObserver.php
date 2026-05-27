<?php

namespace App\Observers;

use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created($model): void
    {
        if (!Auth::check() && !app()->runningInConsole()) {
            return;
        }

        $modelName = class_basename($model);
        $identifier = $this->getIdentifier($model);
        $action = "Membuat {$modelName}: {$identifier}";

        AuditService::log($action, null, $model->getAttributes());
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated($model): void
    {
        if (!Auth::check() && !app()->runningInConsole()) {
            return;
        }

        $modelName = class_basename($model);
        $identifier = $this->getIdentifier($model);
        $action = "Mengubah {$modelName}: {$identifier}";

        $dirty = $model->getDirty();
        $oldValues = [];
        $newValues = [];

        foreach ($dirty as $key => $value) {
            // Ignore timestamps
            if (in_array($key, ['created_at', 'updated_at'])) {
                continue;
            }
            $oldValues[$key] = $model->getOriginal($key);
            $newValues[$key] = $value;
        }

        // Only log if there are actual changes
        if (count($oldValues) > 0) {
            AuditService::log($action, $oldValues, $newValues);
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted($model): void
    {
        if (!Auth::check() && !app()->runningInConsole()) {
            return;
        }

        $modelName = class_basename($model);
        $identifier = $this->getIdentifier($model);
        $action = "Menghapus {$modelName}: {$identifier}";

        AuditService::log($action, $model->getAttributes(), null);
    }

    /**
     * Get a human-readable identifier for the model.
     */
    protected function getIdentifier($model): string
    {
        if (isset($model->order_number)) {
            return $model->order_number;
        }
        
        if (isset($model->name)) {
            return $model->name;
        }

        return '#' . $model->id;
    }
}
