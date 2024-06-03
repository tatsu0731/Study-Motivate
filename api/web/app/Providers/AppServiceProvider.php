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
        $this->app->bind('App\Services\AttendUserService', 'App\Services\AttendUserService');
        $this->app->bind('App\Services\AbsentUserService', 'App\Services\AbsentUserService');
        $this->app->bind('App\Services\UnansweredUserService', 'App\Services\UnansweredUserService');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
