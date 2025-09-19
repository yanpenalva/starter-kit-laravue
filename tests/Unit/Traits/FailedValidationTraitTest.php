<?php
declare(strict_types = 1);

use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\Stubs\FakeClassTraitValidator;

test('it throws an HttpResponseException with detailed validation errors', function () {
    $fake = new FakeClassTraitValidator;
    $invalidInput = [];

    try {
        $fake->validate($invalidInput);
    } catch (HttpResponseException $exception) {
        $response = $exception->getResponse();
        $content = json_decode($response->getContent(), true);

        expect($response->getStatusCode())->toBe(422);

        expect($content)->toHaveKey('errors');
        expect($content['errors'])->toHaveKey('field');
        expect($content['errors']['field'])->toEqual(['The field field is required.']);
    }
})->group('traits');
