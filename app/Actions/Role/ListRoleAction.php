<?php

declare(strict_types=1);

namespace App\Actions\Role;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Fluent;
use Spatie\Permission\Models\Role;

final readonly class ListRoleAction
{
    /**
     * @param Fluent<string, mixed> $params
     * @return LengthAwarePaginator<int, Role>|Collection<int, Role>
     */
    public function execute(Fluent $params): LengthAwarePaginator|Collection
    {
        return Role::query()
            ->with(['permissions' => fn($query) => $query->select(['id', 'description', 'name'])])
            ->when($params->get('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->whereLike('id', "%$search%")
                        ->orWhereLike('name', "%$search%")
                        ->orWhereLike('description', "%$search%");
                });
            })
            ->when($params->get('order'), function ($query) use ($params) {
                return $query->orderBy(
                    match ($params->get('column', 'id')) {
                        'name' => 'name',
                        'description' => 'description',
                        default => 'id',
                    },
                    $params->get('order', 'asc')
                );
            })
            ->when(
                $params->get('paginated', false),
                fn($query) => $query->paginate($params->get('limit', 10)),
                fn($query) => $query->get()
            );
    }
}
