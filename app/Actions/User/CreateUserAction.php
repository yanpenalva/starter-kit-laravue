<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Mail\SendRandomPassword;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\{DB, Mail};
use Illuminate\Support\Fluent;
use Spatie\Permission\Models\Role;

final readonly class CreateUserAction
{
    use LogsActivity;

    /**
     * @param  Fluent<string, mixed>  $params
     */
    public function execute(Fluent $params): User
    {
        return DB::transaction(function () use ($params): User {
            $sendRandom = (bool) $params->get('send_random_password', false);

            $password = $sendRandom
                ? bcrypt($generated = \Illuminate\Support\Str::password(8))
                : $params->get('password');

            $user = User::create([
                'name' => $params->get('name'),
                'email' => $params->get('email'),
                'password' => $password,
                'cpf' => $params->get('cpf'),
                'active' => $params->get('active', false) ? 'true' : 'false',
            ]);

            $user->syncRoles([$params->get('role_id')]);

            if ($sendRandom) {
                Mail::to($user)->queue(new SendRandomPassword($user, $generated));
            }

            $this->writeOnLog($user);

            return $user;
        });
    }

    private function writeOnLog(User $user): void
    {
        /** @var Role|null $role */
        $role = $user->roles()->first();

        $description = sprintf(
            'Criou um novo usuário "%s" com perfil %s',
            $user->name,
            $role->name ?? 'Nenhum perfil associado'
        );

        $this->logCreateActivity(
            activityName: 'users',
            model: $user,
            description: $description,
        );
    }
}
