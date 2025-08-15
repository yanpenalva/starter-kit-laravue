<?php

declare(strict_types = 1);

namespace Tests\Unit\DTO;

use App\DTO\AbstractDTO;

beforeEach(function () {
    $this->sampleDto = new class ('John Doe', 30, ['admin', 'editor']) extends AbstractDTO {
        public function __construct(
            public string $name,
            public int $age,
            public array $roles
        ) {
        }
    };
});

it('converts DTO to array correctly', function () {
    $array = $this->sampleDto->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['name', 'age', 'roles'])
        ->and($array['name'])->toBe('John Doe')
        ->and($array['age'])->toBe(30)
        ->and($array['roles'])->toBe(['admin', 'editor']);
})->group('dto');

it('converts DTO to JSON correctly', function () {
    $json = $this->sampleDto->toJson();

    expect($json)->toBeJson()
        ->and(json_decode($json, true))->toHaveKeys(['name', 'age', 'roles'])
        ->and(json_decode($json, true)['name'])->toBe('John Doe');
})->group('dto');

it('serializes DTO to JSON via jsonSerialize()', function () {
    $serialized = $this->sampleDto->jsonSerialize();

    expect($serialized)->toBeArray()
        ->and($serialized['name'])->toBe('John Doe')
        ->and($serialized['age'])->toBe(30)
        ->and($serialized['roles'])->toBe(['admin', 'editor']);
})->group('dto');
