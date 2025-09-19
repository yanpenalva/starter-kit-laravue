<?php

declare(strict_types = 1);

namespace App\Actions\Auth;

use App\Exceptions\{InvalidCredentialsException, UnactivatedUserException};
use App\Models\User;
use Illuminate\Support\Facades\{Auth, Session};
use Illuminate\Support\Fluent;

final readonly class LoginAction
{
    /**
     * @param  Fluent<string, string>  $params
     * @return array<string, mixed>
     */
    public function execute(Fluent $params): array
    {
        $credentials = ['email' => $params->get('email'), 'password' => $params->get('password')];

        throw_if(! Auth::guard('web')->attempt($credentials), InvalidCredentialsException::class);

        /** @var User|null $user */
        $user = Auth::guard('web')->user();

        throw_if(! $user || ! $user->active, UnactivatedUserException::class);

        Session::regenerate();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'access_token' => $user->createToken('API Token')->plainTextToken,
        ];
    }
}
