<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @method static void created(\Closure|callable|array|class-string $callback)
 * @method static void updated(\Closure|callable|array|class-string $callback)
 * @method static void deleted(\Closure|callable|array|class-string $callback)
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->recordActivity('created', 'Membuat data baru pada ' . class_basename($model));
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $model->recordActivity('updated', 'Memperbarui data ' . class_basename($model), [
                'old' => array_intersect_key($model->getOriginal(), $changes),
                'new' => $changes,
            ]);
        });

        static::deleted(function ($model) {
            $model->recordActivity('deleted', 'Menghapus data ' . class_basename($model));
        });
    }

    public function recordActivity(string $event, string $description, array $properties = []): void
    {
        // Jangan pernah simpan password ke log, walau dalam bentuk hash
        if (isset($properties['old']['password'])) unset($properties['old']['password']);
        if (isset($properties['new']['password'])) unset($properties['new']['password']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'subject_type' => static::class,
            'subject_id' => $this->getKey(),
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
