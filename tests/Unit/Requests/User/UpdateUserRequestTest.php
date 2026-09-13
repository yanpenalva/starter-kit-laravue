<?php

declare(strict_types = 1);

namespace Tests\Unit\Requests\User;

use App\Http\Requests\User\UpdateUserRequest;
use ReflectionMethod;

describe('UpdateUserRequest', function () {
    it('returns the validated payload as a Fluent instance', function () {
        $request = new UpdateUserRequest();
        $validator = validator(['active' => 1], ['active' => 'required']);
        $validator->passes();
        $request->setValidator($validator);

        expect($request->fluentParams()->toArray())->toBe(['active' => 1]);
    });

    it('returns an empty payload when a scalar validation key is requested', function () {
        $request = new UpdateUserRequest();
        $validator = validator(['active' => 1], ['active' => 'required']);
        $validator->passes();
        $request->setValidator($validator);

        expect($request->fluentParams('active')->toArray())->toBe([]);
    });

    it('removes an empty role id before validation', function () {
        $request = UpdateUserRequest::create('/users/1', 'PUT', ['role_id' => '']);
        $method = new ReflectionMethod(UpdateUserRequest::class, 'prepareForValidation');
        $method->invoke($request);

        expect($request->has('role_id'))->toBeFalse();
    });
})->group('userRequest', 'updateUserRequest');
