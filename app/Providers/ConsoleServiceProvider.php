<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\SendWeeklyNewsletterCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    protected $commands = [
        SendWeeklyNewsletterCommand::class,
    ];

    public function boot()
    {
        $this->commands($this->commands);
    }
}


