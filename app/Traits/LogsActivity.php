<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

trait LogsActivity {
    private function extractAttributesWithDates(Model $model, array $attributes): array {
        return collect($attributes)
            ->map(function ($value, $key) use ($model) {
                if (in_array($key, $model->getDates(), true) && $model->{$key} instanceof DateTimeInterface) {
                    return $model->{$key};
                }
                return $value;
            })
            ->toArray();
    }

    public function logUpdateActivity(
        string $activityName,
        Model $model,
        array $dirty,
        string $description = 'Updated record',
        array $relations = [],
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        $beforeAttributes = $oldData ?? $this->extractAttributesWithDates($model, $model->getOriginal());
        $afterAttributes  = $newData ?? $this->extractAttributesWithDates($model, array_merge($model->getOriginal(), $dirty));

        $changes = [
            'before' => $beforeAttributes,
            'after'  => $afterAttributes,
        ];

        foreach ($relations as $key => $value) {
            if (is_string($key) && is_array($value) && count($value) === 2) {
                [$beforeRelation, $afterRelation] = $value;
                $changes['before'][$key] = $beforeRelation;
                $changes['after'][$key]  = $afterRelation;
                continue;
            }

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


    public function logDeleteActivity(
        string $activityName,
        Model $model,
        string $description = 'Deleted record'
    ): void {
        $before = $this->extractAttributesWithDates($model, $model->toArray());
        $before['deleted_at'] = now()
            ->timezone('America/Sao_Paulo')
            ->format('d/m/Y H\hi\m\i\n');

        activity($activityName)
            ->event('delete')
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => ['before' => $before]])
            ->log($description);
    }
}
