<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
    use CreatesApplication;

    protected $seed = true;

    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        if (
            getenv('APP_ENV') !== 'testing' &&
            (!isset($_ENV['APP_ENV']) || $_ENV['APP_ENV'] !== 'testing')
        ) {
            fwrite(STDERR, "❌ APP_ENV is not 'testing' (current: '" . getenv('APP_ENV') . "'). Aborting tests.\n");

            exit(1);
        }
    }
}
