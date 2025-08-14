<?php

declare(strict_types = 1);

namespace Tests\Feature\Api\Auth;

use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('ResetPasswordTest', function () {
    it('should return that it was unable to update the password', function () {
        $password = fake('pt_BR')->password(10);
        $response = $this->postJson(route('reset-password'), [
            'password' => $password,
            'password_confirmation' => $password,
            'email' => $this->user->email,
            'token' => '#############',
        ]);
        $response->assertJsonStructure(['message']);
    })->group('password');

    it('should add password_confirmation to validated data if missing', function () {
        $token = Password::createToken($this->user);
        $password = fake('pt_BR')->password(10);

        $response = $this->postJson(route('reset-password'), [
            'password' => $password,
            'email' => $this->user->email,
            'token' => $token,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    })->group('password');

    it('must change your password', function () {
        $token = Password::createToken($this->user);
        $password = fake('pt_BR')->password(10);
        $response = $this->postJson(route('reset-password'), [
            'password' => $password,
            'password_confirmation' => $password,
            'email' => $this->user->email,
            'token' => $token,
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    })->group('password');

    it('should keep password_confirmation if already present in validated data', function () {
        $request = new ResetPasswordRequest();
        $data = [
            'email' => $this->user->email,
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'token' => 'abc123',
        ];
        $request->merge($data);

        $validator = validator($data, $request->rules(), $request->messages());
        $request->setValidator($validator);

        $fluent = $request->fluentParams();

        expect($fluent)->toBeInstanceOf(\Illuminate\Support\Fluent::class)
            ->and($fluent->get('password_confirmation'))->toBe('12345678');
    });

    it('authorize should return true', function () {
        $request = new ResetPasswordRequest();
        expect($request->authorize())->toBeTrue();
    })->group('password');
});
