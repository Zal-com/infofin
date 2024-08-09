<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
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

        FilamentColor::register([
            'danger' => Color::hex('#EA4335'),
            'warning' => Color::hex('#FBBC05'),
            'info' => Color::hex('#4285F4'),
            'success' => Color::hex('#34A853'),
            'primary' => Color::hex('#1A73E8'),
            'secondary' => Color::hex('#F3F4F6'),
            'tertiary' => Color::hex('#202124'),
        ]);

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
