<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Incident;
use App\Models\Complaint;
use App\Models\LostFound;
use App\Models\Violation;
use App\Models\Certificate;
use App\Models\Referral;
use Illuminate\Pagination\Paginator;
use App\Observers\UserActionObserver;
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
        Paginator::useBootstrap();
        LostFound::observe(UserActionObserver::class);
        Incident::observe(UserActionObserver::class);
        Complaint::observe(UserActionObserver::class);
        Certificate::observe(UserActionObserver::class);
        Violation::observe(UserActionObserver::class);
        Referral::observe(UserActionObserver::class);
        User::observe(classes: UserActionObserver::class);
    }


    protected $listen = [
     \Illuminate\Auth\Events\Login::class => [
        \App\Listeners\LogSuccessfulLogin::class,
    ],
];
}
