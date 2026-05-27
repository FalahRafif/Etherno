<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventSilentlyDiscardingAttributes($this->app->isLocal());

        $this->loadMigrationsFrom(database_path('migrations/1.0.0'));
        $this->loadMigrationsFrom(database_path('migrations/1.1.3'));
        $this->loadMigrationsFrom(database_path('migrations/1.1.4'));
        $this->loadMigrationsFrom(database_path('migrations/1.1.6'));
        $this->loadMigrationsFrom(database_path('migrations/1.1.7'));
        $this->loadMigrationsFrom(database_path('migrations/1.1.8'));
        $this->loadMigrationsFrom(database_path('migrations/1.1.9'));
        $this->loadMigrationsFrom(database_path('migrations/1.1.10'));
    }
}
