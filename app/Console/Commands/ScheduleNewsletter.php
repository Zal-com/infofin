<?php

namespace App\Console\Commands;

use App\Models\NewsletterSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
            $date = Carbon::now();
            $dayNow = $date->dayOfWeek();
            $hour = $date->format('H:i');
            $formattedTime = Carbon::createFromFormat('H:i:s', $newsletterSchedule->send_time)->format('H:i');

            if ($dayNow == $newsletterSchedule->day_of_week && $hour === $formattedTime) {
                Artisan::call('newsletter:send');
            }
        }
    }
}
