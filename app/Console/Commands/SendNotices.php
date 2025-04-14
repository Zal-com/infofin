<?php

namespace App\Console\Commands;

use App\Jobs\SendDeletionNotices;
use App\Jobs\SendDeletionNoticesAndDeleteInactiveUsers;
use Illuminate\Console\Command;

class SendNotices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notices:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail to inactive users to tell them their account is going to be deleted';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendDeletionNotices::dispatch();
        $this->info('Notices sent successfully');
    }
}
