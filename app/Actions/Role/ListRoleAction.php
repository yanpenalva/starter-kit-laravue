<?php

declare(strict_types = 1);

namespace App\Actions\Role;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\{Builder, Collection};
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, Relation};
use Illuminate\Support\Fluent;
use Spatie\Permission\Models\{Permission, Role};

final readonly class ListRoleAction {
    /**
     * @phpstan-param Fluent<string, mixed> $params
     *
     * @phpstan-return LengthAwarePaginator<int, Role>
     */
    public function execute(Fluent $params): LengthAwarePaginator|Collection {
        return Role::query()
            ->with([
                /** @param BelongsToMany<Permission, Role> $relation */
                'permissions' => static function (Relation $relation): void {
                    $relation->select(['id', 'description', 'name']);
                },
            ])
            ->when($params->get('search'), function (Builder $query, mixed $search): void {
                assert(is_string($search));
                $query->where(static function (Builder $query) use ($search): void {
                    $query->whereLike('id', "%{$search}%")
                        ->orWhereLike('name', "%{$search}%")
                        ->orWhereLike('description', "%{$search}%");
                });
            })
            ->when($params->get('order'), function (Builder $query) use ($params): Builder {
                $column = $params->get('column', 'id');
                $direction = $params->get('order', 'asc');

                assert(is_string($column));
                assert(is_string($direction));

                return $query->orderBy($column, $direction);
            })
            ->when(
                $params->get('paginated', true) === true,
                function (Builder $query) use ($params): LengthAwarePaginator {
                    $limit = $params->get('limit', 10);
                    assert(is_int($limit));

                    return $query->paginate($limit);
                },
                fn (Builder $query): LengthAwarePaginator => $query->paginate()
            );
    }
}
