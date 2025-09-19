<?php
declare(strict_types = 1);

return [

    /*
    |--------------------------------------------------------------------------
    | Configurações iniciais
    |--------------------------------------------------------------------------
    |
    | Aqui voce pode configurar as opções para: usuários, perfis, permissões entre outras configurações.
    |
    */

    'admin' => [
        'email' => env('APP_ADMIN_EMAIL'),
        'password' => env('APP_ADMIN_PASSWORD'),
        'role' => env('APP_ADMIN_ROLE'),
    ],
    'der' => [
        'key' => env('APP_DER_KEY'),
    ],
];
