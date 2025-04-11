<?php

declare(strict_types=1);

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

        $query->when($params->get('search'), fn($query, $search) => $query->where(function ($query) use ($search) {
            $query->whereLike('users.id', "%$search%")
                ->orWhereLike('users.name', "%$search%")
                ->orWhereLike('users.email', "%$search%")
                ->orWhereLike('roles.name', "%$search%");
        }));
        $query->when($params->get('order'), fn($query, $order) => $query->orderBy(
            match ($params->get('column', 'id')) {
                'name' => 'users.name',
                'email' => 'users.email',
                'role' => 'roles.name',
                'setSituation' => 'users.active',
                default => 'users.id',
            },
            $order
        ));

        return $params->get('paginated', false)
            ? $query->paginate($params->get('limit', 10))
            : $query->get();
    }
}
