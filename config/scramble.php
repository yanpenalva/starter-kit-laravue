<?php declare(strict_types = 1);

// use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [

    'api_path' => 'api/v1/',

    'api_domain' => null,

    'export_path' => 'api.json',

    'info' => [

        'version' => env('API_VERSION', '0.0.1'),

        'description' => '',
    ],

    'ui' => [

        'title' => null,

        'theme' => 'light',

        'hide_try_it' => false,

        'logo' => '',

        'try_it_credentials_policy' => 'include',

        'auth' => [
            'type' => 'bearer',
            'in' => 'header',
            'name' => 'Authorization',
            'placeholder' => 'Bearer {token}',
        ],
    ],

    'servers' => null,

    'middleware' => [
        'web',
        // RestrictedDocsAccess::class,
        \App\Http\Middleware\RestrictScrambleAccess::class,

    ],

    'extensions' => [],
];
