<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Actions\Role\RoleBySlugAction;
use App\Mail\SendVerifyEmail;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\{DB, Mail};
use Illuminate\Support\Fluent;
use Spatie\Permission\Models\Role;

final readonly class CreateExternalUserAction
{
    use LogsActivity;

    /**
     * @phpstan-param Fluent<string, mixed> $params
     */
    public function execute(Fluent $params): User
    {
        return DB::transaction(function () use ($params): User {
            $user = User::create([
                'name' => $params->get('name'),
                'email' => $params->get('email'),
                'password' => $params->get('password'),
                'cpf' => $params->get('cpf'),
                'active' => $params->get('active', false) ? 'true' : 'false',
            ]);

            $slug = $params->get('role');
            assert(is_string($slug));

            $role = app(RoleBySlugAction::class)->execute($slug);

            $user->syncRoles([$role->id]);

            $this->writeOnLog($user);

            Mail::to($user)->queue(new SendVerifyEmail($user));

            return $user;
        });
    }

    private function writeOnLog(User $user): void
    {
        /** @var Role|null $role */
        $role = $user->roles()->first();

        $this->logGeneralActivity(
            activityName: 'Gestão de Usuários',
            model: $user,
            description: sprintf(
                'Criou um novo usuário "%s" com %d permissões',
                $user->email,
                $role?->permissions()->count() ?? 0
            ),
            event: 'create'
        );
    }
}
