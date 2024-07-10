<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use phpCAS;

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
        Schema::defaultStringLength(191);


        Gate::before(function ($user, $ability){
            return $user->hasRole('admin') ? true : null;
        });

        if(config('app.env') !== 'local') {
            \URL::forceScheme('https');
        }
    }
}
