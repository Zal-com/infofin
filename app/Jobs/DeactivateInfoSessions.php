<?php

namespace App\Jobs;

use App\Models\InfoSession;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeactivateInfoSessions implements ShouldQueue
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
        Log::info("InfoSessions cleanup starting !");

        $info_sessions = InfoSession::where('status', 1)->get();

        foreach ($info_sessions as $info_session) {
            $info_session->updateQuietly(['status' => Carbon::now()->isAfter($info_session->session_datetime) ? 0 : 1]);
            Log::info("InfoSessions cleanup updated : ID " . $info_session->id . "'s status set to 0");
        }
        Log::info("InfoSessions cleanup terminated !");

    }
}
