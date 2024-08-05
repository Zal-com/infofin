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
use Illuminate\Support\Facades\Mail;

class SendWeeklyNewsletter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
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


            //Tous les projets de moins d'une semaine qui ont les memes info_types que les centres d'interet de l'utilisateur + vérifier le domaine scientifique

            $projects = Project::where('created_at', '>=', now()->subWeek())->get();

            // Filtrer par domaines scientifiques de l'utilisateur
            $userScientificDomains = $subscriber->scientific_domains->pluck('id')->toArray();
            $projects = $projects->filter(function ($project) use ($userScientificDomains) {
                $projectScientificDomains = $project->scientific_domains->pluck('id')->toArray();
                return !empty(array_intersect($projectScientificDomains, $userScientificDomains));
            });

            // Filtrer par info_types de l'utilisateur
            $userInfoTypes = $subscriber->info_types->pluck('id')->toArray();
            $projects = $projects->filter(function ($project) use ($userInfoTypes) {
                $projectInfoTypes = $project->info_types->pluck('id')->toArray();
                return !empty(array_intersect($projectInfoTypes, $userInfoTypes));
            });

            if (!$projects->isEmpty()) {
                $data['projects'] = $projects;
                Mail::to($subscriber->email)->send(new WeeklyNewsletter($data));
            }


        }
    }
}
