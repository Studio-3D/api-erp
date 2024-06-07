<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            'App\Repositories\V1\SocieteRepository',
            'App\Repositories\V1\SocieteRepositoryDefault'
        );
        $this->app->bind(
            'App\Services\V1\SocieteService',
            'App\Services\V1\SocieteServiceDefault'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
