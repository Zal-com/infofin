<?php

namespace App\Jobs;

use App\Mail\WeeklyNewsletter;
use App\Models\NewsletterSchedule;
use App\Models\Project;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
        $is_send = false;

        $message = NewsletterSchedule::first();

        foreach ($subscribers as $subscriber) {

            $data = [
                'prenom' => $subscriber->first_name,
                'message' => $message->message,
            ];

            $token = $this->jwtService->generateUnsubscribeJWT($subscriber->id);
            $url = url('/unsubscribe') . '?token=' . $token;

            $data['url'] = $url;

            $projects = Project::where('is_in_next_email', 1)
                ->where(function ($query) use ($subscriber) {
                    // Condition sur les scientific_domains
                    $query->whereHas('scientific_domains', function ($query) use ($subscriber) {
                        $query->whereIn('scientific_domain_id', $subscriber->scientific_domains->pluck('id'));
                    })
                        // Condition sur (activities OU expenses)
                        ->where(function ($query) use ($subscriber) {
                            $query->whereHas('activities', function ($query) use ($subscriber) {
                                $query->whereIn('activity_id', $subscriber->activities->pluck('id'));
                            })
                                ->orWhereHas('expenses', function ($query) use ($subscriber) {
                                    $query->whereIn('expense_id', $subscriber->expenses->pluck('id'));
                                });
                        });
                })
                ->where("is_big", 0)
                ->get();


            $projects = $projects->merge(
                Project::where('is_big', 1)->where('is_in_next_email', 1)->get()
            );

            if (!$projects->isEmpty()) {
                $data['projects'] = $projects;
                Mail::to($subscriber->email)->send(new WeeklyNewsletter($data));
                $is_send = true;
            }
        }

        $summaryMessage = $is_send ? 'A mail has been sent.' : 'No mail has been sent.';

        Mail::raw($summaryMessage, function ($message) {
            $message->to('maxime.vanhoren@ulb.be')
                ->subject('Weekly Newsletter Summary');
        });

        Project::where('is_in_next_email', 1)
            ->update(['is_in_next_email' => 0]);

        NewsletterSchedule::first()->update(["message" => null]);
    }
}
