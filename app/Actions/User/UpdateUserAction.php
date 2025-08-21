<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Mail;

final readonly class UpdateUserAction
{
    use LogsActivity;

    /**
     * @param Fluent<string, mixed> $params
     */
    public function execute(Fluent $params, int|string $id): User
    {
        return DB::transaction(function () use ($id, $params): User {
            /** @var User $user */
            $user = User::findOrFail($id);

            $oldData = (array) $user->only($user->getFillable());
            $oldRoles = $user->roles->pluck('name')->all();
            $oldData['roles'] = $oldRoles;

            $this->applyFillableUpdates($user, $params);

            $dirty = (array) $user->getDirty();
            $user->save();

            $this->applyRoles($user, $params);

            $newData = (array) $user->only($user->getFillable());
            $newRolesNames = $user->roles->pluck('name')->all();
            $newData['roles'] = $newRolesNames;

            if ($oldData['roles'] !== $newData['roles']) {
                $dirty['roles'] = true;
            }

            if ((bool) $params->get('notify_status', false)) {
                Mail::to($user)->queue(new \App\Mail\SendNotificationUserActivation($user));
            }

            $this->logUpdateActivity(
                'users',
                $user,
                $dirty,
                'Atualizou um usuário',
                [],
                $oldData,
                $newData
            );

            return $user->load('roles');
        });
    }

    /**
     * @param Fluent<string, mixed> $params
     */
    private function applyFillableUpdates(User $user, Fluent $params): void
    {
        $fillableParams = array_intersect_key(
            $params->toArray(),
            array_flip($user->getFillable())
        );

        $user->fill($fillableParams);
    }

    /**
     * @param Fluent<string, mixed> $params
     */
    private function applyRoles(User $user, Fluent $params): void
    {
        if ($params->filled('role_id')) {
            $user->syncRoles([$params->get('role_id')]);
        }
    }
}
