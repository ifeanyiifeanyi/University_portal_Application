<?php

namespace App\Providers;

use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthService::class, function ($app) {
            return new AuthService(
                Auth::guard(),
                $app->make(RateLimiter::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
