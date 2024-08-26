<?php

namespace App\Jobs;

use App\Mail\WeeklyNewsletter;
use App\Models\Project;
use App\Models\User;
use App\Services\JWTService;
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

    protected $jwtService;

    public function __construct(JWTService $jwtService = null)
    {
        $this->jwtService = $jwtService ?: app(JWTService::class);
    }

    public function handle()
    {
        $subscribers = User::where('is_email_subscriber', 1)->get();

        foreach ($subscribers as $subscriber) {

            $data = [
                'prenom' => $subscriber->first_name,
            ];

            $token = $this->jwtService->generateUnsubscribeJWT($subscriber->id);
            $url = url('/unsubscribe') . '?token=' . $token;

            $data['url'] = $url;

            $projects = Project::where('is_in_next_email', 1)
                ->where(function ($query) use ($subscriber) {
                    $query->whereHas('scientific_domains', function ($query) use ($subscriber) {
                        $query->whereIn('scientific_domain_id', $subscriber->scientific_domains->pluck('id'));
                    })
                        ->orWhereHas('info_types', function ($query) use ($subscriber) {
                            $query->whereIn('info_type_id', $subscriber->info_types->pluck('id'));
                        });
                })
                ->get();

            if (!$projects->isEmpty()) {
                $data['projects'] = $projects;
                Mail::to($subscriber->email)->send(new WeeklyNewsletter($data));
            }
        }

        Project::where('is_in_next_email', 1)
            ->update(['is_in_next_email' => 0]);
    }
}
