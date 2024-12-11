<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use EightyNine\FilamentAdvancedWidget\AdvancedWidgetConfiguration;
use Illuminate\Support\Str;

class TotalProjects extends BaseWidget
{
    protected int|string|array $columnSpan = '6';
    protected int $currentMonthRegistrations;
    protected int $previousMonthRegistrations;
    protected float $percentageDifference;
    protected bool $isPositiveTrend;

    protected function getStats(): array
    {

        $now = Carbon::now();
        $currentMonth = $now->format('Y-m');
        $previousMonth = $now->subMonth()->format('Y-m');

        $this->currentMonthRegistrations = Project::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])
            ->count();

        $this->previousMonthRegistrations =
            Project::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$previousMonth])
                ->count();

        // Calcul de la tendance
        $this->percentageDifference = $this->previousMonthRegistrations > 0
            ? round(
                (($this->currentMonthRegistrations - $this->previousMonthRegistrations)
                    / $this->previousMonthRegistrations) * 100,
                1
            )
            : 0;

        $this->isPositiveTrend = $this->percentageDifference >= 0;

        return [
            Stat::make("Total d'appels", Project::all()->count())
                ->icon('heroicon-o-newspaper')
                ->iconPosition('start')
                ->description("+{$this->currentMonthRegistrations} nouveaux appels ce mois-ci")
                ->descriptionIcon($this->isPositiveTrend ? 'heroicon-o-chevron-up' : 'heroicon-o-chevron-down', 'before')
                ->descriptionColor($this->isPositiveTrend ? 'success' : 'danger')
                ->iconColor('warning')
        ];
    }

    protected function getColumns(): int
    {
        return 6;
    }
}
