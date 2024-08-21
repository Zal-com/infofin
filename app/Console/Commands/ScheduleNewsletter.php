<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\NewsletterSchedule;

class ScheduleNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:newsletter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dynamically schedule the newsletter:send command';

    /**
     * Execute the console command.
     */
    public function handle(Schedule $schedule): void
    {
        $newsletterSchedule = NewsletterSchedule::first();

        if ($newsletterSchedule && $newsletterSchedule->is_active) {
            $schedule->command('newsletter:send')
                ->weeklyOn($newsletterSchedule->day_of_week, $newsletterSchedule->send_time);
        }
    }
}
