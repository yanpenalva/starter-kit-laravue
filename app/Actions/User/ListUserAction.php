<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Fluent;

final readonly class ListUserAction
{
    /**
     * @param Fluent<string, mixed> $params
     * @return LengthAwarePaginator<int, User>|Collection<int, User>
     */
    public function execute(Fluent $params): LengthAwarePaginator|Collection
    {
        $query = User::query()->with('roles');

        $query->select([
            'users.*',
            'roles.name as role',
        ])
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id');

        /** @var string|null $search */
        $search = $params->get('search');

        if (is_string($search) && $search !== '') {
            $query->where(function ($query) use ($search) {
                $query->whereLike('users.id', "%{$search}%")
                    ->orWhereLike('users.name', "%{$search}%")
                    ->orWhereLike('users.email', "%{$search}%")
                    ->orWhereLike('roles.name', "%{$search}%");
            });
        }

        /** @var string|null $order */
        $order = $params->get('order');

        /** @var string|null $column */
        $column = $params->get('column', 'id');

        if (is_string($order)) {
            $query->orderBy(
                match ($column) {
                    'name' => 'users.name',
                    'email' => 'users.email',
                    'role' => 'roles.name',
                    'setSituation' => 'users.active',
                    default => 'users.id',
                },
                $order
            );
        }

        /** @var bool $paginated */
        $paginated = (bool) $params->get('paginated', false);

        /** @var int $limit */
        $limit = is_numeric($params->get('limit')) ? (int) $params->get('limit') : 10;

        return $paginated
            ? $query->paginate($limit)
            : $query->get();
    }
}
