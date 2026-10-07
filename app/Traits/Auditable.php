<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'Создание записи ' . class_basename($model),
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'details' => $model->getAttributes(),
            ]);
        });

        static::updated(function ($model) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'Обновление записи ' . class_basename($model),
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'details' => $model->getChanges(), // Записываются только измененные поля
            ]);
        });

        static::deleted(function ($model) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'Удаление записи ' . class_basename($model),
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'details' => $model->toArray(),
            ]);
        });
    }
}