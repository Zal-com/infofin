<?php

namespace App\Filament\Widgets;

use App\Models\User;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;


class SubscribersOverview extends BaseWidget
{

    protected int|string|array $columnSpan = 7;

    protected function getStats(): array
    {
        return [
            Stat::make("Abonnés newsletter", User::where('is_email_subscriber', 1)->count())->icon('heroicon-o-envelope')
                ->iconPosition('start')
                ->iconColor('warning'),
        ];
    }

    protected function getColumns(): int
    {
        return 7;
    }
}
