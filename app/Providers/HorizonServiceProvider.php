<?php

declare(strict_types = 1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\{Horizon, HorizonApplicationServiceProvider};

// @codeCoverageIgnoreStart
final class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Horizon::auth(function ($request) {
            return Gate::allows('viewHorizon', [$request->user()]);
        });
    }

    protected function gate(): void
    {
        Gate::define('viewHorizon', fn ($user) => $user && $user->isAdmin());
    }
}

// @codeCoverageIgnoreEnd
