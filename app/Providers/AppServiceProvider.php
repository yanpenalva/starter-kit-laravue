<?php

declare(strict_types = 1);

namespace App\Providers;

use Barryvdh\Debugbar\Facades\Debugbar;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\{
    OpenApi,
    SecurityScheme
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{DB, Vite};
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment() !== 'local') {
            Debugbar::disable();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureModelBehavior();
        $this->configureDatabase();
        $this->configureRequest();
        $this->configureScramble();
        $this->configureVite();

        if (!class_exists(Telescope::class)) {
            return;
        }

        $app = $this->app;

        if (method_exists($app, 'runningInQueue') && $app->runningInQueue()) {
            Telescope::stopRecording();
        }
    }

    /**
     * Configure the keys for database configur
     *
     * @codeCoverageIgnore
     */
    public function configureDatabase(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
    }

    /**
     * Configure the behavior of Eloquent models.
     */
    protected function configureModelBehavior(): void
    {
        Model::preventLazyLoading(!$this->app->isProduction());
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();
    }

    /**
     * Configure HTTPS for non-local environments.
     */
    protected function configureRequest(): void
    {
        $this->app['request']->server->set('HTTPS', $this->app->environment() !== 'local');
    }

    /**
     * Configure the Scramble package.
     */
    protected function configureScramble(): void
    {
        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );
        });
    }

    /**
     * Configure the Vite prefetch strategy.
     */
    protected function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }
}
