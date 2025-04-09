<?php

namespace App\Jobs;

use App\Mail\ThreeMonthDeletionNotice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendDeletionNotices implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $now = Carbon::now();

        // Définir clairement les périodes
        $twoYearsAgo = $now->copy()->subYears(2);
        $twoWeeksBeforeTwoYears = $twoYearsAgo->copy()->addWeeks(2);
        $threeMonthNoticeDate = $now->copy()->subMonths(21);

        // 1 seule requête pour récupérer tous les utilisateurs concernés
        $users = User::where('last_login', '<=', $threeMonthNoticeDate)->get();

        foreach ($users as $user) {
            $lastLogin = Carbon::parse($user->last_login);

            if ($lastLogin->between($twoYearsAgo, $twoWeeksBeforeTwoYears)) {
                // Rappel 2 semaines avant suppression
                Mail::to($user->email)->send(new TwoWeeksDeletionNotice(['first_name' => $user->first_name]));
            } else {
                // Rappel 3 mois avant suppression
                Mail::to($user->email)->send(new ThreeMonthDeletionNotice(['first_name' => $user->first_name]));
            }
        }
    }
}

