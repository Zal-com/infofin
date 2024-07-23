<?php

namespace App\Jobs;

use App\Mail\WeeklyNewsletter;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyNewsletter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        Log::info('Handling SendWeeklyNewsletter job');
        $subscribers = User::where('is_email_subscriber', 1)->get();
        Log::info('Subscribers retrieved: ' . $subscribers->count());

        foreach ($subscribers as $subscriber) {
            Log::info('Sending email to: ' . $subscriber->email);
            Mail::to($subscriber->email)->send(new WeeklyNewsletter());
        }
    }

}
