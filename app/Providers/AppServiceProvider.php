<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Services\AuthService;
use App\Policies\TicketPolicy;
use App\Models\RecurringPaymentPlan;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use App\Observers\RecurringPaymentPlanObserver;

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
    public function boot(): void
    {
        Paginator::useBootstrap();

        Ticket::observe(TicketPolicy::class);

        RecurringPaymentPlan::observe(RecurringPaymentPlanObserver::class);

    }
}
