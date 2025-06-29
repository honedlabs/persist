<?php

declare(strict_types=1);

namespace Honed\Persist;

use Illuminate\Support\ServiceProvider;

class PersistServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/persist.php', 'persist');

        $this->app->singleton(PersistManager::class, fn ($app) => new PersistManager($app));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/persist.php' => config_path('persist.php'),
        ], 'persist-config');
    }
}
