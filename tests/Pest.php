<?php

declare(strict_types=1);

use Illuminate\Support\Facades\{Event, Http, Mail};
use Illuminate\Support\Facades\Storage;

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

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function something() {
    //
}
