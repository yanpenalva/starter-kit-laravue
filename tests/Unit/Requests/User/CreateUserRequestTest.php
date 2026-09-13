<?php

declare(strict_types = 1);

namespace Tests\Unit\Http\Requests\User;

use App\Http\Requests\User\CreateUserRequest;
use Illuminate\Support\{Fluent, Str};

describe('CreateUserRequest', function () {
    it('authorizes request', function () {
        $request = new CreateUserRequest();
        expect($request->authorize())->toBeTrue();
    });

    it('returns correct attributes', function () {
        $request = new CreateUserRequest();

        expect($request->attributes())->toHaveKeys([
            'name',
            'email',
            'password',
            'role_id',
            'active',
            'cpf',
            'registration',
            'send_random_password',
        ]);
    });

    it('returns correct messages', function () {
        $request = new CreateUserRequest();

        expect($request->messages())->toHaveKeys([
            'cpf.unique',
            'email.unique',
            'email.email',
            'boolean',
            'required',
            'exists',
            'max',
            'unique',
            'in',
            'password.min',
        ]);
    });

    it('returns rules including password when random password not sent and role is not admin', function () {
        $request = new CreateUserRequest();
        $request->merge([
            'send_random_password' => false,
            'role_id' => 2,
        ]);

        $rules = $request->rules();

        expect($rules)->toHaveKey('password')
            ->and($rules['password'])->toContain('required', 'min:8');
    });

    it('omits password rule when random password is sent', function () {
        $request = new CreateUserRequest();

        $request->merge([
            'send_random_password' => true,
            'role_id' => 2,
        ]);
        expect($request->rules())->not->toHaveKey('password');

        $request = new CreateUserRequest();
        $request->merge([
            'send_random_password' => false,
            'role_id' => 1,
        ]);
        expect($request->rules())->toHaveKey('password');
    });

    it('returns Fluent with random password when send_random_password is true', function () {
        $stub = new class {
            public bool $send_random_password = true;

            public string $password = '';

            public function boolean(string $key): bool
            {
                return $key === 'send_random_password';
            }

            public function fluentParams(): Fluent
            {
                $validated = ['name' => 'Yan', 'email' => 'test@ex.com'];

                return new Fluent(array_merge($validated, [
                    'password' => $this->boolean('send_random_password') ? Str::password(8) : $this->password,
                ]));
            }
        };

        $fluent = $stub->fluentParams();

        expect($fluent)->toBeInstanceOf(Fluent::class)
            ->and(mb_strlen($fluent->password))->toBe(8);
    });

    it('returns Fluent with provided password when random not requested', function () {
        $stub = new class {
            public bool $send_random_password = false;

            public string $password = '12345678';

            public function boolean(string $key): bool
            {
                return false;
            }

            public function fluentParams(): Fluent
            {
                $validated = ['name' => 'Yan', 'email' => 'test@ex.com', 'password' => $this->password];

                return new Fluent(array_merge($validated, [
                    'password' => $this->boolean('send_random_password') ? Str::password(8) : $this->password,
                ]));
            }
        };

        $fluent = $stub->fluentParams();

        expect($fluent->password)->toBe('12345678');
    });

    it('returns validated data and a generated password from a real request', function () {
        $request = new CreateUserRequest();
        $request->merge(['send_random_password' => true]);

        $validator = validator(
            ['name' => 'Yan', 'email' => 'test@example.com'],
            ['name' => 'required', 'email' => 'required']
        );
        $validator->passes();
        $request->setValidator($validator);

        $fluent = $request->fluentParams();

        expect($fluent->get('name'))->toBe('Yan')
            ->and(mb_strlen($fluent->get('password')))->toBe(8);
    });

    it('returns an empty validated payload for a scalar key and the provided password', function () {
        $request = new CreateUserRequest();
        $request->merge([
            'send_random_password' => false,
            'password' => '12345678',
        ]);

        $validator = validator(['name' => 'Yan'], ['name' => 'required']);
        $validator->passes();
        $request->setValidator($validator);

        $fluent = $request->fluentParams('name');

        expect($fluent->toArray())->toBe(['password' => '12345678']);
    });
})->group('userRequest', 'createUserRequest');
