<?php declare(strict_types = 1);
use App\Enums\RolesEnum;

dataset(
    'validUser',
    [
        fn () => [
            'cpf' => preg_replace('/\D/', '', fake('pt_BR')->cpf()),
            'name' => fake('pt_BR')->name(),
            'email' => fake('pt_BR')->email(),
            'password' => fake('pt_BR')->password(10),
            'active' => fake('pt_BR')->randomElement([0, 1]),
            'send_random_password' => true,
            'role_id' => fake('pt_BR')->randomElement([1]),
        ],
    ]
);

dataset('userJsonValidStructure', [
    fn () => [
        'data' => [
            'id',
            'name',
            'email',
            'cpf',
            'active',
            'email_verified_at',
            'created_at',
            'updated_at',
            'roles',
        ],
    ],
]);

dataset('createUserJsonValidStructure', [
    fn () => [
        'data' => [
            'id',
            'name',
            'email',
            'cpf',
            'active',
            'created_at',
            'updated_at',
        ],
    ],
]);

dataset('validJsonStructure', [
    fn () => [
        'data' => [
            'id',
            'name',
            'email',
            'cpf',
            'active',
            'email_verified_at',
            'created_at',
            'updated_at',
            'roles',
        ],
    ],
]);

dataset('paginationStructure', [
    fn () => [
        'current_page',
        'data',
        'first_page_url',
        'from',
        'last_page',
        'links',
        'next_page_url',
        'path',
        'last_page_url',
        'per_page',
        'prev_page_url',
        'to',
        'total',
    ],
]);

dataset('nameNotProvided', [
    fn () => [
        'name' => null,
        'email' => fake('pt_BR')->email(),
        'password' => fake('pt_BR')->password(10),
        'active' => fake('pt_BR')->randomElement([0, 1]),
        'send_random_password' => true,
        'role_id' => fake('pt_BR')->randomElement([1]),
    ],
]);

$pass = fake('pt_BR')->password(8);

dataset('registerUser', [
    fn () => [
        'name' => fake('pt_BR')->name(),
        'email' => fake('pt_BR')->email(),
        'password' => $pass,
        'password_confirmation' => $pass,
        'cpf' => preg_replace('/\D/', '', fake('pt_BR')->cpf()),
        'role' => RolesEnum::GUEST->value,
    ],
]);

dataset(
    'updateUserData',
    [
        fn () => [
            'cpf' => preg_replace('/\D/', '', fake('pt_BR')->cpf()),
            'name' => fake('pt_BR')->name(),
            'email' => fake('pt_BR')->email(),
            'password' => fake('pt_BR')->password(10),
            'active' => fake('pt_BR')->randomElement([0, 1]),
            'role_id' => fake('pt_BR')->randomElement([1]),
        ],
    ]
);

dataset('invalidEmail', [
    fn () => [
        'name' => fake('pt_BR')->name(),
        'email' => fake('pt_BR')->text(),
        'password' => fake('pt_BR')->password(10),
        'active' => fake('pt_BR')->randomElement([0, 1]),
        'send_random_password' => true,
        'role_id' => fake('pt_BR')->randomElement([1]),
    ],
]);

dataset('emailAlreadyExists', [
    fn () => [
        'name' => fake('pt_BR')->name(),
        'email' => createUsers()->first()->email,
        'password' => fake('pt_BR')->password(10),
        'active' => 1,
        'send_random_password' => true,
        'role_id' => fake('pt_BR')->randomElement(['1']),
    ],
]);

dataset('invalidRole', [
    fn () => [
        'name' => fake('pt_BR')->name(),
        'email' => fake('pt_BR')->email(),
        'password' => fake('pt_BR')->password(10),
        'active' => 10,
        'send_random_password' => true,
        'role_id' => fake('pt_BR')->randomElement(['10000']),
    ],
]);

dataset('invalidStatus', [
    fn () => [
        'name' => fake('pt_BR')->name(),
        'email' => fake('pt_BR')->email(),
        'password' => fake('pt_BR')->password(10),
        'active' => 10,
        'send_random_password' => true,
        'role_id' => fake('pt_BR')->randomElement([1]),
    ],
]);

dataset('invalidRoleData', [
    fn () => [
        'name' => fake('pt_BR')->name(),
        'email' => fake('pt_BR')->email(),
        'password' => fake('pt_BR')->password(10),
        'active' => 10,
        'send_random_password' => true,
        'role_id' => fake('pt_BR')->randomElement(['10000']),
    ],
]);

dataset('emailNotAvailable', [
    fn () => [
        'name' => fake('pt_BR')->name(),
        'email' => 'test@test.com',
        'password' => $pass,
        'password_confirmation' => $pass,
        'cpf' => preg_replace('/\D/', '', fake('pt_BR')->cpf()),
        'role' => RolesEnum::GUEST->value,
    ],
]);
