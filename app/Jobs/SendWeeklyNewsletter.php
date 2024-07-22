<?php

namespace App\Jobs;

use App\Mail\WeeklyNewsletter;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        $subscribers = User::where('is_email_subscriber', 1)->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(new WeeklyNewsletter());
        }
    }
}
