<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait LogsActivity {
    /**
     * Logs an update activity.
     *
     * @param string $activityName Nome da atividade (ex: 'roles')
     * @param Model $model Modelo afetado
     * @param array<string, mixed> $newData Dados alterados (getDirty)
     * @param string $description Descrição do evento
     * @param array<int|string, string|array<int, mixed>> $relations
     *        - Se valor for string: nome da relação (ex: 'permissions')
     *        - Se valor for array: snapshots manuais [before, after]
     */
    public function logUpdateActivity(
        string $activityName,
        Model $model,
        array $newData,
        string $description = 'Updated record',
        array $relations = []
    ): void {
        $before = collect($model->getOriginal());
        $after  = $before->merge($newData);

        $changes = [
            'before' => $before->toArray(),
            'after'  => $after->toArray(),
        ];

        foreach ($relations as $key => $value) {
            // Caso 1: snapshot manual ["permissions" => [$old, $new]]
            if (is_string($key) && is_array($value) && count($value) === 2) {
                [$beforeRelation, $afterRelation] = $value;
                $changes['before'][$key] = $beforeRelation;
                $changes['after'][$key]  = $afterRelation;
                continue;
            }

            // Caso 2: nome de relação simples (ex: "permissions")
            if (is_string($value) && method_exists($model, $value)) {
                $changes['before'][$value] = $model->{$value}()->pluck('name')->all();
                $changes['after'][$value]  = $model->refresh()->{$value}()->pluck('name')->all();
            }
        }

        activity($activityName)
            ->event('update')
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => $changes])
            ->log($description);
    }

    /**
     * Logs a delete activity.
     *
     * @param string $activityName
     * @param Model $model
     * @param string $description
     */
    public function logDeleteActivity(
        string $activityName,
        Model $model,
        string $description = 'Deleted record'
    ): void {
        activity($activityName)
            ->event('delete')
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => ['before' => $model->toArray()]])
            ->log($description);
    }

    /**
     * Logs a general activity.
     *
     * @param string $activityName
     * @param Model $model
     * @param string $description
     * @param string $event
     */
    public function logGeneralActivity(
        string $activityName,
        Model $model,
        string $description,
        string $event = 'view'
    ): void {
        activity($activityName)
            ->event($event)
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => $model->toArray()])
            ->log($description);
    }
}
