<?php

declare(strict_types=1);

namespace App\Traits;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

trait LogsActivity {
    /**
     * @param array<mixed> $attributes
     * @return array<string, mixed>
     */
    private function extractAttributesWithDates(Model $model, array $attributes): array {
        /** @var array<string, mixed> $normalized */
        $normalized = collect($attributes)
            ->map(function (mixed $value, string|int $key) use ($model): mixed {
                return is_string($key)
                    && in_array($key, $model->getDates(), true)
                    && $model->{$key} instanceof DateTimeInterface
                    ? $model->{$key}
                    : $value;
            })
            ->all();

        return $normalized;
    }

    /**
     * @param array<string, mixed> $dirty
     * @param array<string, mixed> $relations
     * @param array<string, mixed>|null $oldData
     * @param array<string, mixed>|null $newData
     */
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
                /** @var Relation<Model, Model, Collection<int, string>> $relation */
                $relation = $model->{$value}();

                /** @var array<int, string> $before */
                $before = $relation->pluck('name')->all();
                /** @var array<int, string> $after */
                $after  = $relation->pluck('name')->all();

                $changes['before'][$value] = $before;
                $changes['after'][$value]  = $after;
            }
        }

        if (isset($beforeAttributes['roles']) && isset($afterAttributes['roles'])) {
            $changes['before']['roles'] = $beforeAttributes['roles'];
            $changes['after']['roles']  = $afterAttributes['roles'];
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
        /** @var array<string, mixed> $before */
        $before = $this->extractAttributesWithDates($model, $model->toArray());
        $before['deleted_at'] = now()
            ->timezone('America/Sao_Paulo')
            ->format('d/m/Y H\hi\m\i\n');

        if (method_exists($model, 'roles')) {
            /** @var Relation<Model, Model, Collection<int, string>> $relation */
            $relation = $model->roles();

            /** @var array<int, string> $roles */
            $roles = $relation->pluck('name')->all();
            $before['roles'] = $roles;
        }

        activity($activityName)
            ->event('delete')
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => ['before' => $before]])
            ->log($description);
    }

    public function logCreateActivity(
        string $activityName,
        Model $model,
        string $description,
    ): void {
        /** @var array<string, mixed> $after */
        $after = $model->toArray();

        if (method_exists($model, 'roles')) {
            /** @var Relation<Model, Model, Collection<int, string>> $relation */
            $relation = $model->roles();

            /** @var array<int, string> $roles */
            $roles = $relation->pluck('name')->all();
            $after['roles'] = $roles;
        }

        activity($activityName)
            ->event('create')
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->withProperties(['attributes' => ['after' => $after]])
            ->log($description);
    }
}
