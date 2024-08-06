<?php

namespace App\Livewire;

use App\Models\Project;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Support\Carbon;

class CalendarWidget extends FullCalendarWidget
{
    //protected static string $view = 'livewire.calendar-widget';

    public function fetchEvents(array $fetchInfo): array
    {
        $startDate = Carbon::parse($fetchInfo['start']);
        $endDate = Carbon::parse($fetchInfo['end']);

        $projects = Project::whereJsonContains('deadlines', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })->get();

        $events = [];

        foreach ($projects as $project) {
            foreach ($project->upcomingDeadlines() as $deadline) {
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
