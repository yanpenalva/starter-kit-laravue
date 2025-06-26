<?php declare(strict_types = 1);

namespace Tests\Feature\Api\Auth;

use App\Enums\RolesEnum;
use App\Mail\SendForgetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\{DB, Mail};
use Str;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('ForgotPasswordTest', function () {

    it('should return that email is required', function () {
        $payload = ['email' => null];
        $response = $this->postJson(route('forgot-password'), $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'O campo e-mail é obrigatório.',
                    ],
                ],
            ]);
    })->group('password');

    it('should queue the SendForgetPasswordMail when a valid guest requests password reset', function () {

        $user = User::factory()->create();

        DB::table('roles')->updateOrInsert(
            ['slug' => RolesEnum::GUEST->value],
            [
                'name' => 'GUEST',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $role = DB::table('roles')->where('slug', RolesEnum::GUEST->value)->first();
        $user->assignRole([$role->id]);

        $payload = ['email' => $user->email];
        $response = $this->postJson(route('forgot-password'), $payload);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Mail::assertQueued(SendForgetPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    });

    it('should return that the email is invalid', function () {
        $payload = ['email' => fake('pt_BR')->text(20)];
        $response = $this->postJson(route('forgot-password'), $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'O campo e-mail deve ser um endereço de e-mail válido.',
                    ],
                ],
            ]);
    })->group('password');

    it('should return that no user with that email address was found', function () {
        $payload = ['email' => fake('pt_BR')->email()];
        $response = $this->postJson(route('forgot-password'), $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'Nenhum cadastro encontrado com o e-mail informado.',
                    ],
                ],
            ]);
    })->group('password');

    it('should return that the user profile cannot request a password change', function () {
        $payload = ['email' => $this->user->email];
        $response = $this->postJson(route('forgot-password'), $payload);
        $response->assertJsonStructure(['message']);
    })->group('password');

    it('should return status 204', function () {
        $user = User::factory()->create();

        DB::table('roles')->updateOrInsert(
            ['slug' => RolesEnum::GUEST],
            [
                'name' => 'Guest',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $role = DB::table('roles')->where('slug', RolesEnum::GUEST)->first();

        $user->assignRole([$role->id]);

        $payload = ['email' => $user->email];
        $response = $this->postJson(route('forgot-password'), $payload);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    })->group('password');

    it('builds the mailable correctly with markdown and variables', function () {
        $user = User::factory()->make([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $token = Str::random(60);

        $mailable = new SendForgetPasswordMail($token, $user);
        $rendered = $mailable->render();

        expect($mailable->envelope()->subject)->toBe('Password Recovery Request SP System 1.0');
        expect($rendered)->toContain('John Doe');
        expect($rendered)->toContain("email={$user->email}");
    })->group('password');
});
