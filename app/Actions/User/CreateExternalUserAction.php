<?php

declare(strict_types = 1);

namespace App\Actions\User;

use App\Actions\Role\RoleBySlugAction;
use App\Mail\SendVerifyEmail;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\{DB, Mail};
use Illuminate\Support\Fluent;

final readonly class CreateExternalUserAction {
    use LogsActivity;

    /**
     * @phpstan-param Fluent<string, mixed> $params
     */
    public function execute(Fluent $params): User {
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

            Mail::to($user)->queue(new SendVerifyEmail($user));

            return $user;
        });
    }
}
