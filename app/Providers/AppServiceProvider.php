<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
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
        Gate::define('isAdmin', function () {
            return Auth::id() == 1;
        });

        Route::pattern('from', '\d{4}-\d{2}-\d{2}');
        Route::pattern('to', '\d{4}-\d{2}-\d{2}');
        Route::pattern('date', '\d{4}-\d{2}-\d{2}');
        Route::pattern('fromDate', '\d{4}-\d{2}-\d{2}');
        Route::pattern('toDate', '\d{4}-\d{2}-\d{2}');
    }
}
