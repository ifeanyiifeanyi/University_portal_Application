<?php

namespace App\Providers;

use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* AuthService is being instantiated on how the Auth facade is being used in its class */
        $this->app->bind(AuthService::class, function ($app) {
            return new AuthService(Auth::guard());
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
