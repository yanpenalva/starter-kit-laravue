<?php declare(strict_types = 1);

use Illuminate\Support\Facades\{Event, Http, Mail};

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function () {
        Http::preventStrayRequests();
        Mail::fake();
        Event::fake();
    })
    ->in('Feature');

pest()->extend(Tests\TestCase::class)
    ->in('Unit');

pest()->printer()->compact();

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function something()
{
    //
}
