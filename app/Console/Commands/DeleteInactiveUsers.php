<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all users who did not log in for 2 years';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Jobs\DeleteInactiveUsers::dispatch();
        $this->info('Inactive users have been deleted');
    }
}
