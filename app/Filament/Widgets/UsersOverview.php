<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use EightyNine\FilamentAdvancedWidget\AdvancedWidgetConfiguration;


class UsersOverview extends BaseWidget
{
    protected int|string|array $columnSpan = '7';

    protected int $currentMonthRegistrations;
    protected int $previousMonthRegistrations;
    protected float $percentageDifference;
    protected bool $isPositiveTrend;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $currentMonth = $now->format('Y-m');
        $previousMonth = $now->subMonth()->format('Y-m');

        $this->currentMonthRegistrations = User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])
            ->count();

        $this->previousMonthRegistrations =
            User::whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$previousMonth])
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
            Stat::make("Total d'utilisateurs", User::all()->count())->icon('heroicon-o-users')
                ->iconPosition('start')
                ->description("+{$this->currentMonthRegistrations} nouveaux utilisateurs ce mois-ci")
                ->descriptionIcon($this->isPositiveTrend ? 'heroicon-o-chevron-up' : 'heroicon-o-chevron-down', 'before')
                ->descriptionColor($this->isPositiveTrend ? 'success' : 'danger')
                ->iconColor('warning'),
        ];
    }


    protected function getColumns(): int
    {
        return 7;
    }
}
