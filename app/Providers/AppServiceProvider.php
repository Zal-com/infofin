<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
    public function boot(Schedule $schedule): void
    {

        $schedule->command('newsletter:send')->weeklyOn(1, '15:23'); // 1 = Monday
        Schema::defaultStringLength(191);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        if (config('app.env') !== 'local') {
            \URL::forceScheme('https');
        }
    }
}
