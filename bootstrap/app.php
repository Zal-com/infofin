<?php

use App\Jobs\ArchiveProject;
use App\Jobs\DailyDeleteProject;
use BezhanSalleh\FilamentExceptions\FilamentExceptions;
use Bilfeldt\LaravelRouteStatistics\Http\Middleware\RouteStatisticsMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            RouteStatisticsMiddleware::class,
            \App\Http\Middleware\TrustProxies::class,
        ]);
        $middleware->alias([
            'contributor' => \App\Http\Middleware\ContributorMiddleware::class
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(new DailyDeleteProject())->dailyAt('02:00');
        $schedule->job(new ArchiveProject())->weeklyOn(1, '03:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {
            FilamentExceptions::report($e);
        });
    })->create();


