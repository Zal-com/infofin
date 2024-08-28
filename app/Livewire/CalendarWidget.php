<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    //protected static string $view = 'livewire.calendar-widget';

    public function fetchEvents(array $fetchInfo): array
    {
        $start = Carbon::parse($fetchInfo['start'])->toDateString();
        $end = Carbon::parse($fetchInfo['end'])->toDateString();
        $cacheKey = "events_{$start}_{$end}";

        // Check if the events are already cached
        $events = Cache::remember($cacheKey, 60, function () use ($start, $end, $fetchInfo) {
            $projects = Project::whereRaw("
                JSON_CONTAINS_PATH(deadlines, 'one', '$[*].date')
            ")->get(['id', 'title', 'deadlines', 'is_big']);

            $events = [];

            foreach ($projects as $project) {
                foreach ($project->deadlines as $deadline) {
                    $deadlineDate = Carbon::parse($deadline['date']);
                    if ($deadlineDate >= $fetchInfo['start'] && $deadlineDate <= $fetchInfo['end']) {
                        $events[] = [
                            'title' => $project->title,
                            'start' => $deadlineDate->format('Y-m-d'),
                            'end' => $deadlineDate->format('Y-m-d'),
                            'url' => route('projects.show', $project->id),
                            'color' => $project->is_big ? 'crimson' : null,
                            'height' => 'auto',
                        ];
                    }
                }
            }

            return $events;
        });

        return $events;
    }

    public function config(): array
    {
        $thisMonth = Carbon::today()->startOfMonth();
        $sixMonthsLater = $thisMonth->copy()->startOfMonth(6);
        $nineMonthsLater = $thisMonth->copy()->addMonths(9);

        return [
            'selectable' => false,
            'editable' => false,
            'timeZone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'multiMonthThreeMonth,dayGridMonth',
            ],
            'initialView' => 'multiMonthThreeMonth',
            'views' => [
                'multiMonthThreeMonth' => ['type' => 'multiMonth', 'duration' => ['months' => 3], 'buttonText' => '3 mois'],
            ],
            'multiMonthMaxColumns' => 2, // force 2 columns
            'validRange' => [
                'start' => $thisMonth->format('Y-m-d'),
                'end' => $nineMonthsLater->format('Y-m-d'),
            ],
        ];
    }
}
