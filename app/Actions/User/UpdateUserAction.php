<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Mail;

final readonly class UpdateUserAction {
    use LogsActivity;

    /**
     * @param Fluent<string, mixed> $params
     */
    public function execute(Fluent $params, int|string $id): User {
        return DB::transaction(function () use ($id, $params): User {
            /** @var User $user */
            $user = User::findOrFail($id);

            /** @var array<string, mixed> $oldData */
            $oldData = (array) $user->only($user->getFillable());
            /** @var array<int, string> $oldRoles */
            $oldRoles = $user->roles->pluck('name')->all();
            $oldData['roles'] = $oldRoles;

            /** @var array<string, mixed> $fillableParams */
            $fillableParams = array_intersect_key(
                $params->toArray(),
                array_flip($user->getFillable())
            );

            $user->fill($fillableParams);

            /** @var array<string, mixed> $dirty */
            $dirty = (array) $user->getDirty();
            $user->save();

            /** @var array<int, int|string|null> $newRoles */
            $newRoles = [$params->get('role_id')];
            $user->syncRoles($newRoles);

            /** @var array<string, mixed> $newData */
            $newData = (array) $user->only($user->getFillable());
            /** @var array<int, string> $newRolesNames */
            $newRolesNames = $user->roles->pluck('name')->all();
            $newData['roles'] = $newRolesNames;

            if ($oldData['roles'] !== $newData['roles']) {
                $dirty['roles'] = true;
            }

            $notify = (bool) $params->get('notify_status', false);
            if ($notify) {
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
}
