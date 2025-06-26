<?php declare(strict_types = 1);

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('ResetPasswordTest', function () {
    it('should return that it was unable to update the password ', function () {
        $password = fake('pt_BR')->password(10);
        $response = $this->postJson(route('reset-password'), [
            'password' => $password,
            'password_confirmation' => $password,
            'email' => $this->user->email,
            'token' => '#############',
        ]);
        $response->assertJsonStructure(['message']);
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
});
