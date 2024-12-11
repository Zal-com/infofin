<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DailyDeleteProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $projects = Project::where('status', 1)->get();

        foreach ($projects as $project) {
            $allDeadlinesPassed = true;
            $allContinuousZero = true;

            foreach ($project->deadlines as $deadline) {
                $deadlineDate = Carbon::parse($deadline['date']);

                if ($deadlineDate->isFuture()) {
                    $allDeadlinesPassed = false;
                }

                if ($deadline['continuous'] == 1) {
                    $allContinuousZero = false;
                    break;
                }
            }

            if ($allDeadlinesPassed && $allContinuousZero) {
                $project->status = 0;
                $project->timestamps = false;
                $project->save();
            }
        }
    }
}
