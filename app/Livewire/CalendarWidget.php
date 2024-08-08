<?php

namespace App\Livewire;

use App\Models\Project;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

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
            ")->get(['id', 'title', 'deadlines']);

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
        $today = Carbon::today();
        $sixMonthsLater = $today->copy()->addMonths(6);
        return [
            'selectable' => false,
            'editable' => false,
            'timeZone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'initialView' => 'dayGridMonth',
            'validRange' => [
                'start' => $today->format('Y-m-d'),
                'end' => $sixMonthsLater->format('Y-m-d'),
            ],
        ];
    }
}
