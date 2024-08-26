<?php

namespace App\Console\Commands;

use App\Jobs\SendNewsletterPrevisu;
use Illuminate\Console\Command;

class SendNewsletterPrevisuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:previsu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send previsu mail to admin';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        SendNewsletterPrevisu::dispatch();
        $this->info('Weekly newsletter has been sent.');
    }
}
