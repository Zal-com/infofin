<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ArchiveProject implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        ////
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all projects with status = 1 or status = 0
        $projects = Project::whereIn('status', [0, 1])->get();

        foreach ($projects as $project) {

            // Assuming the deadlines are stored in an array format in the 'deadlines' field
            $deadlines = $project->deadlines;

            if (is_array($deadlines) && !empty($deadlines)) {
                // Check if any deadline has 'continuous' set to true
                $hasContinuousDeadline = collect($deadlines)->contains(function ($deadline) {
                    return isset($deadline['continuous']) && $deadline['continuous'] === true;
                });

                if ($hasContinuousDeadline) {
                    // Skip this project if there is a continuous deadline
                    Log::info("Project ID {$project->id} has a continuous deadline. Skipping.");
                    continue;
                }

                // Extract the dates from the deadlines and find the latest one
                $latestDeadline = collect($deadlines)->map(function ($deadline) {
                    return Carbon::parse($deadline['date']);
                })->max();

                // Check if the latest deadline is more than 5 years ago
                if ($latestDeadline->lt(Carbon::now()->subYears(5))) {
                    // Update the project status to -1 without modifying the updated_at timestamp
                    Log::info("Archiving project ID {$project->id} due to old deadline.");
                    $project->status = -1;
                    $project->timestamps = false; // Disable timestamps for this save
                    $project->save();
                }
            } else {
                Log::info("No valid deadlines found for project ID: {$project->id}");
            }
        }
    }
}
