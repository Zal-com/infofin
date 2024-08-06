<?php

namespace App\Livewire;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Support\Carbon;

class CalendarWidget extends FullCalendarWidget
{
    //protected static string $view = 'livewire.calendar-widget';

    public function fetchEvents(array $fetchInfo): array
    {
        return [];
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
