<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class TotalProjects extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("Total d'appels", Project::all()->count())
                ->icon('heroicon-o-newspaper')
        ];
    }
}
