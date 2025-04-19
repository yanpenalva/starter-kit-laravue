<?php

namespace Tests\Feature\Users;

use App\Mail\SendRandomPassword;
use App\Mail\SendVerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Mail;

use function Pest\Laravel\{actingAs, get, post};

use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    createRoles();
    $this->users = createUsers();
    $this->asAdmin = asAdmin();
    $this->invalidId = 111111;
});

describe('Users Management', function () {
    describe('Listing Users', function () {
        it(
            'should return a 200 status code and proper JSON structure',
            function (array $paginationStructure) {

                actingAs($this->asAdmin)
                    ->get(route('users.list'))
                    ->assertStatus(Response::HTTP_OK)
                    ->assertJsonStructure($paginationStructure);
            }
        )->with('paginationStructure');
    });

    describe('Querying User Data', function () {
        it(
            'should return a 200 status code and proper JSON structure for valid user ID',
            function (array $paginationStructure) {

                actingAs($this->asAdmin)
                    ->get(route('users.list', [$this->asAdmin->id]))
                    ->assertStatus(Response::HTTP_OK)
                    ->assertJsonStructure($paginationStructure);
            }
        )->with('paginationStructure');

        it('should return a 404 status code for invalid user ID', function () {
            actingAs($this->asAdmin)
                ->get(route('users.show', $this->invalidId))
                ->assertStatus(Response::HTTP_NOT_FOUND);
        });
    });

    describe('Registering Users', function () {
        it(
            'should return a 200 status code and proper JSON structure for valid user data',
            function (array $registerUser, array $validJsonStructure) {

                actingAs($this->asAdmin)
                    ->post(route('users.register'), $registerUser)
                    ->assertStatus(Response::HTTP_OK)
                    ->assertExactJson(["message" => "Um e-mail de confirmação foi encaminhado para {$registerUser['email']}. Por favor, realize os procedimentos para ativação da sua conta."]);
            }
        )->with('registerUser')
            ->with('validJsonStructure');

        it(
            'should return a 201 status when create a new user',
            function (array $validUser, array $createUserJsonValidStructure) {

                actingAs($this->asAdmin)
                    ->post(route('users.create'), $validUser)
                    ->assertStatus(Response::HTTP_CREATED)
                    ->assertJsonStructure($createUserJsonValidStructure);
            }
        )->with('validUser')
            ->with('createUserJsonValidStructure');

        it(
            'should return a 422 status code when name is not provided',
            function (array $nameNotProvided) {

                actingAs($this->asAdmin)
                    ->post(route('users.create'), $nameNotProvided)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'name' => [
                                'O campo Nome é obrigatório.',
                            ],
                        ],
                    ]);
            }
        )->with('nameNotProvided');

        it('should return a 422 status code when email is invalid', function (array $invalidEmail) {

            actingAs($this->asAdmin)
                ->post(route('users.create'), $invalidEmail)
                ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJson([
                    'errors' => [
                        'email' => [
                            'O E-mail inserido não é válido.',
                        ],
                    ],
                ]);
        })->with('invalidEmail');

        it(
            'should return a 422 status code when the email entered has already been registered',
            function (array $emailAlreadyExists) {

                actingAs($this->asAdmin)
                    ->post(route('users.create'), $emailAlreadyExists)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'email' => [
                                'O e-mail já foi cadastrado.',
                            ],
                        ],
                    ]);
            }
        )->with('emailAlreadyExists');

        it(
            'should return a 422 status code when status informed user is invalid',
            function (array $invalidStatus) {

                actingAs($this->asAdmin)
                    ->post(route('users.create'), $invalidStatus)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'active' => [
                                'O Status inserido não é válido.',
                            ],
                        ],
                    ]);
            }
        )->with('invalidStatus');

        it(
            'should return a 422 status code when role informed user is invalid',
            function (array $invalidRole) {

                actingAs($this->asAdmin)
                    ->post(route('users.create'), $invalidRole)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'role_id' => [
                                'O Perfil é inválido.',
                            ],
                        ],
                    ]);
            }
        )->with('invalidRole');
    });

    describe('Updating Users', function () {
        it(
            'should return a 200 status code and proper JSON structure for valid user ID and data',
            function (array $updateDataUser, array $userJsonValidStructure) {

                actingAs($this->asAdmin)
                    ->put(route('users.update', [$this->asAdmin->id]), $updateDataUser)
                    ->assertStatus(Response::HTTP_OK)
                    ->assertJsonStructure($userJsonValidStructure);
            }
        )
            ->with('updateUserData')
            ->with('userJsonValidStructure');

        it(
            'should return a 422 status code when name is not provided',
            function (array $nameNotProvided) {

                actingAs($this->asAdmin)
                    ->put(route('users.update', [$this->asAdmin->id]), $nameNotProvided)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'name' => [
                                'O campo Nome é obrigatório.',
                            ],
                        ],
                    ]);
            }
        )->with('nameNotProvided');

        it(
            'should return a 422 status code when email is invalid',
            function (array $invalidEmail) {

                actingAs($this->asAdmin)
                    ->put(route('users.update', $this->asAdmin->id), $invalidEmail)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'email' => [
                                'O E-mail inserido não é válido.',
                            ],
                        ],
                    ]);
            }
        )->with('invalidEmail');

        it(
            'should return a 422 status code when the email entered has already been registered',
            function (array $emailAlreadyExists) {

                actingAs($this->asAdmin)
                    ->put(route('users.update', [$this->asAdmin->id]), $emailAlreadyExists)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'email' => [
                                'O e-mail já foi cadastrado.',
                            ],
                        ],
                    ]);
            }
        )->with('emailAlreadyExists');

        it(
            'should return a 422 status code when status informed user is invalid',
            function (array $invalidStatus) {

                actingAs($this->asAdmin)
                    ->put(route(
                        'users.update',
                        [$this->asAdmin->id]
                    ), $invalidStatus)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'active' => [
                                'O Status inserido não é válido.',
                            ],
                        ],
                    ]);
            }
        )->with('invalidStatus');

        it(
            'should return a 422 status code when role informed user is invalid',
            function (array $invalidRoleData) {

                actingAs($this->asAdmin)
                    ->put(route(
                        'users.update',
                        [$this->asAdmin->id]
                    ), $invalidRoleData)
                    ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->assertJson([
                        'errors' => [
                            'role_id' => [
                                'O Perfil é inválido.',
                            ],
                        ],
                    ]);
            }
        )->with('invalidRoleData');
    });

    describe('Register Users', function () {
        it('should return status 200 of registered user', function (array $registerUser) {

            $response = post(route('users.register'), $registerUser);
            $message = "Um e-mail de confirmação foi encaminhado para {$registerUser['email']}. Por favor, realize os procedimentos para ativação da sua conta.";
            $response->assertStatus(Response::HTTP_OK)
                ->assertJson(['message' => $message]);
        })->with('registerUser');
    });

    describe('User email verification', function () {
        it('should return that the users email has been verified', function () {

            $user = createUser([
                'name' => 'John Doe',
                'email' => 'B0KpM@example.com',
                'email_verified_at' => null,
            ]);

            $url = createTemporaryUrlForUser($user, Carbon::now()->addHours(Config::get('auth.verification.expire', 48)));

            get($url)
                ->assertStatus(Response::HTTP_OK)
                ->assertJson(['message' => 'O seu cadastro foi verificado com sucesso!']);
        });

        it('should return that the email is already validated', function () {
            $user = createUser([
                'name' => 'John Doe',
                'email' => 'B0KpM@example.com',
                'email_verified_at' => now(),
            ]);

            $url = createTemporaryUrlForUser($user, Carbon::now()->addMinutes(30));

            $response = get($url);

            expect($response->json('message'))->toContain('Seu cadastro já foi validado!');
        });


        it('should return that the token is invalid', function () {

            $user = createUser([
                'email_verified_at' => now(),
            ]);

            $url = createTemporaryUrlForUser($user, Carbon::now()->subHours(1));

            get($url)
                ->assertJson(['message' => 'Invalid signature.']);
        });
    });
    describe('Deleting Users', function () {
        it('should return a 404 status code for invalid user ID', function () {
            actingAs($this->asAdmin)
                ->delete(route('users.delete', $this->invalidId), ['reason' => 'Test deletion reason'])
                ->assertStatus(Response::HTTP_NOT_FOUND)
                ->assertJson(['message' => "No query results for model [App\\Models\\User] {$this->invalidId}"]);
        });

        it('should successfully delete a user and return 204 status code', function () {
            $userToDelete = $this->users[1];

            $response = actingAs($this->asAdmin)
                ->delete(route('users.delete', $userToDelete), ['reason' => 'Test deletion reason']);

            $response->assertStatus(Response::HTTP_NO_CONTENT);
            $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
        });

        it('should return an error status code when trying to delete own account', function () {
            $response = actingAs($this->asAdmin)
                ->delete(route('users.delete', $this->asAdmin), ['reason' => 'Test deletion reason']);

            $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->assertJson([
                    'message' => 'Erro ao deletar usuário.',
                    'error' => 'Não é possível realizar essa ação.',
                ]);

            $this->assertDatabaseHas('users', ['id' => $this->asAdmin->id]);
        });

        it('should return a 401 status code for unauthenticated user', function () {
            $userToDelete = $this->users[1];

            $response = $this->delete(route('users.delete', $userToDelete), ['reason' => 'Test deletion reason']);

            $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        });
    });

    it('builds the SendRandomPassword mail with correct data', function () {
        $user = User::factory()->make([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $password = 'abc123XYZ';

        $mail = new SendRandomPassword($user, $password);

        $rendered = $mail->render();

        expect($mail->envelope()->subject)->toBe('Registration in the SP 1.0 System');
        expect($rendered)->toContain('John Doe');
        expect($rendered)->toContain($password);
    });

    it('queues the SendVerifyEmail when registering external user', function (array $registerUser) {

        $response = post(route('users.register'), $registerUser);

        $response->assertStatus(Response::HTTP_OK);

        Mail::assertQueued(\App\Mail\SendVerifyEmail::class, function ($mail) use ($registerUser) {
            return $mail->hasTo($registerUser['email']);
        });
    })->with('registerUser');

    it('builds the SendVerifyEmail mail with correct data', function () {
        $user = User::factory()->make([
            'id' => 1,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        Carbon::setTestNow(Carbon::now());

        $mail = new SendVerifyEmail($user);

        $rendered = $mail->render();

        $expectedLink = \URL::temporarySignedRoute(
            'users.verify',
            Carbon::now()->addHours(Config::get('auth.verification.expire', 48)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );


        expect($mail->envelope()->subject)->toBe('Confirmação de cadastro');
        expect($rendered)->toContain('Jane Doe');
    });

})->group('users');
