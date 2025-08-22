<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\{Bus, Event, Http, Mail, Queue, Storage};

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function () {
        Http::preventStrayRequests();
        Mail::fake();
        Event::fake();
        Storage::fake('local');
        Queue::fake();
        Bus::fake();
    })
    ->in('Feature');

pest()->extend(Tests\TestCase::class)
    ->in('Unit');

pest()->printer()->compact();

expect()->extend('toBeUuid', function () {
    return $this->toMatch('/^[0-9a-fA-F-]{36}$/');
});
