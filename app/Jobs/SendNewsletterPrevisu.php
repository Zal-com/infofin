<?php

namespace App\Jobs;

use App\Mail\WeeklyNewsletter;
use App\Models\NewsletterSchedule;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsletterPrevisu implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {

        $message = NewsletterSchedule::first()->get(['message']);
        $data = [
            'prenom' => "Admin",
            "message" => $message->message,
        ];

        $url = url('/');

        $data['url'] = $url;

        $projects = Project::where('is_in_next_email', 1)
            ->get();

        $adresses = ['daniele.carati@ulb.be', 'antoine.delers@ul.be', 'guillaume.stordeur@ulb.be'];

        if (!$projects->isEmpty()) {
            $data['projects'] = $projects;
            foreach ($adresses as $adress) {
                Mail::to($adress)->send(new WeeklyNewsletter($data));
            }
        }
    }
}
