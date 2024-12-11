<?php

namespace App\Console\Commands;

use App\Jobs\SendWeeklyNewsletter;
use Illuminate\Console\Command;

class SendWeeklyNewsletterCommand extends Command
{
    protected $signature = 'newsletter:send';

    protected $description = 'Send weekly newsletter to email subscribers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        SendWeeklyNewsletter::dispatch();
        $this->info('Weekly newsletter has been sent.');
    }
}
