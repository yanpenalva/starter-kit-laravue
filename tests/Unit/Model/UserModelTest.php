<?php
declare(strict_types = 1);

namespace Tests\Unit\Model;

use App\Models\User;

describe('UserModel Test', function () {
    it('has correct fillable attributes', function () {
        $fillable = ['name', 'email', 'password', 'cpf', 'active', 'email_verified_at'];
        $user = new User;

        expect($user->getFillable())->toEqual($fillable);
    });

    it('has correct hidden attributes', function () {
        $hidden = ['password', 'remember_token'];
        $user = new User;
        expect($user->getHidden())->toEqual($hidden);
    });

    it('has correct casts', function () {
        $casts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'id' => 'int',
        ];
        $user = new User;
        expect($user->getCasts())->toEqual($casts);
    })->group('model');

    it('has correct table name', function () {
        $user = new User;
        expect($user->getTable())->toBe('users');
    });

    it('has correct primary key', function () {
        $user = new User;
        expect($user->getKeyName())->toBe('id');
    });

    it('has correct timestamps', function () {
        $user = new User;
        expect($user->usesTimestamps())->toBe(true);
    });

    it('has correct model name', function () {
        $user = new User;
        expect($user::class)->toBe(User::class);
    })->group('model');
})->group('model');
