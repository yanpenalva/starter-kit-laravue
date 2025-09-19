<?php
declare(strict_types = 1);

namespace Tests\Unit\Architecture;

describe('Architecture Validation Tests', function () {

    arch()
        ->expect('App')
        ->toUseStrictTypes()
        ->not->toUse(['die', 'dd', 'dump']);

    arch('App\Http must be use only in App\Http')
        ->expect('App\Http')
        ->toOnlyBeUsedIn('App\Http');

    // MODELS

    arch('App\Models must be classes and extended from Eloquent\Models')
        ->expect('App\Models')
        ->toBeClasses()
        ->toExtend('Illuminate\Database\Eloquent\Model');

    arch('App\Models must be in the Models namespace')
        ->expect('App\Models')
        ->toStartWith('App\Models');

    // ACTIONS

    arch('App\Actions must be final and readonly classes')
        ->expect('App\Actions')
        ->classes()
        ->toBeFinal()
        ->toBeReadonly();

    // TRAITS

    arch('App\Traits must be in the Traits namespace')
        ->expect('App\Traits')
        ->toStartWith('App\Traits');

    // ENUMS

    arch('App\Enums must be in the Enums namespace')
        ->expect('App\Enums')
        ->toStartWith('App\Enums');

    // SERVICES

    arch('App\Services must be in the Services namespace')
        ->expect('App\Services')
        ->toStartWith('App\Services');

    // PROVIDERS

    arch('App\Providers must be in the Providers namespace')
        ->expect('App\Providers')
        ->toStartWith('App\Providers');

    // CONTROLLERS

    arch('App\Http\Controllers must be in the Http\Controllers namespace')
        ->expect('App\Http\Controllers')
        ->toStartWith('App\Http\Controllers');

    arch('Controllers extends Controller')
        ->expect('App\Http\Controllers')
        ->toExtend('App\Http\Controllers\Controller');

    // REQUESTS

    arch('App\Http\Requests must be in the Http\Requests namespace')
        ->expect('App\Http\Requests')
        ->toStartWith('App\Http\Requests');

    arch('Requests extends FormRequest')
        ->expect('App\Http\Requests')
        ->toExtend('Illuminate\Foundation\Http\FormRequest');

    // RESOURCES

    arch('App\Http\Resources must be in the Http\Resources namespace')
        ->expect('App\Http\Resources')
        ->toStartWith('App\Http\Resources');

    arch('App\Http\Resources must be classes')
        ->expect('App\Http\Resources')
        ->toBeClass();

    arch('App\Http\Resources must be extends from JsonResource')
        ->expect('App\Http\Resources')
        ->toExtend('Illuminate\Http\Resources\Json\JsonResource');

    // MIDDLEWARE

    arch('App\Http\Middleware must be in the Http\Middleware namespace')
        ->expect('App\Http\Middleware')
        ->toStartWith('App\Http\Middleware');

    arch('App\Http\Middleware must be classes')
        ->expect('App\Http\Middleware')
        ->toBeClass();
})->group('arch');
