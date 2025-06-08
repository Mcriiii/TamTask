<?php

namespace App\Providers;

use App\Models\Complaint;
use App\Models\LostFound;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Observers\UserActionObserver;

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
        Complaint::observe(classes: UserActionObserver::class);
        User::observe(classes: UserActionObserver::class);
    }


    protected $listen = [
     \Illuminate\Auth\Events\Login::class => [
        \App\Listeners\LogSuccessfulLogin::class,
    ],
];
}
