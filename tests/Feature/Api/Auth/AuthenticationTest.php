<?php declare(strict_types = 1);

namespace Tests\Feature\Api\Auth;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'PermissionSeeder']);
    Artisan::call('db:seed', ['--class' => 'RoleSeeder']);

    $this->inactiveUser = User::factory()->create([
        'active' => false,
        'password' => 'correctpassword',
    ]);
    $this->activeUser = User::factory()->create([
        'active' => true,
        'password' => 'correctpassword',
    ]);
    $this->userAuth = User::factory()->create();

    $this->roleAdmin = Role::where('slug', RolesEnum::ADMINISTRATOR->value)->first();
    $this->userAuth->assignRole([$this->roleAdmin->id]);

});

describe('Authentication', function () {
    it('does not authenticate a user with an invalid password', function () {
        $payload = ['email' => $this->activeUser->email, 'password' => 'wrongpassword'];

        $response = $this->postJson(route('login'), $payload);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure(['message']);
    });
    it('does not authenticate a user with an invalid email', function () {
        $payload = ['email' => 'nonexistent@example.com', 'password' => 'correctpassword'];

        $response = $this->postJson(route('login'), $payload);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure(['message']);
    });
    it('authenticates a user with correct credentials', function () {
        $payload = [
            'email' => $this->activeUser->email,
            'password' => 'correctpassword',
        ];

        $this->postJson(route('login'), $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonPath('user.id', $this->activeUser->id)
            ->assertJsonPath('user.email', $this->activeUser->email)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'access_token',
            ], );
    });
    it('does not authenticate an inactive user', function () {
        $payload = ['email' => $this->inactiveUser->email, 'password' => 'correctpassword'];

        $response = $this->postJson(route('login'), $payload);

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonStructure(['message']);
    });
    it('logs out a user', function () {
        $token = $this->activeUser->createToken('test-token')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(route('logout'));
        $response->assertStatus(Response::HTTP_OK);
    });

    it('does not log out a user without a token', function () {
        $response = $this->postJson(route('logout'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });

    it('does not log out a user with an invalid token', function () {
        $response = $this->withHeader('Authorization', 'Bearer invalidtoken')
            ->postJson(route('logout'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });

    it('does not log out a user with an expired token', function () {
        $token = $this->activeUser->createToken('test-token')->plainTextToken;
        $this->activeUser->tokens()->update(['created_at' => now()->subMinutes(50)]);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(route('logout'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });

    it('does not log out a user with a revoked token', function () {
        $token = $this->activeUser->createToken('test-token')->plainTextToken;
        $this->activeUser->tokens()->delete();
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(route('logout'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });

    describe('Gets data from the logged-in user', function () {
        it('should return a 200 status code and proper JSON structure', function () {

            $this->actingAs($this->userAuth)
                ->get(route('auth.myProfile'))
                ->assertStatus(Response::HTTP_OK)
                ->assertJsonStructure([
                    'name',
                    'email',
                    'permissions',
                ]);
        });
    })->group('auth');
})->group('auth');
