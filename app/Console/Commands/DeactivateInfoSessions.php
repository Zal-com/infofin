<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeactivateInfoSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'info_session:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates InfoSessions status to 0 if session datetime has passed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Jobs\DeactivateInfoSessions::dispatch();
    }
}
