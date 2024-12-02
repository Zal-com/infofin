<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Carbon\Carbon;
use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AdvancedProjectsChart extends AdvancedChartWidget
{
    //protected static ?string $heading = 'test';
    protected static string $color = 'info';
    protected static ?string $icon = 'heroicon-o-chart-bar';
    protected static ?string $label = 'Graphe des appels ajoutés';
    public ?string $filter = 'year';

    /**
     * @param string|null $heading
     */
    public function setHeading(?string $filter = 'year'): void
    {
        // Définir la plage de dates en fonction du filtre
        [$start, $end] = self::getDateRange($filter);

        // Compter les projets dans la plage définie
        $count = Project::whereBetween('created_at', [$start, $end])->count();
        self::$heading = $count;
    }


    protected function getFilters(): ?array
    {
        return [
            'today' => 'Aujourd\'hui',
            'week' => 'Semaine',
            'month' => 'Mois',
            'year' => 'Année',
        ];
    }


    protected function getData(): array
    {
        [$start, $end] = $this->getDateRange();

        $data = Trend::model(Project::class)
            ->between(
                start: $start,
                end: $end
            )
            ->perMonth()
            ->count();

        self::setHeading();
        return [
            'datasets' => [
                [
                    'label' => 'Projets entrés',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::make($value->date)->format('m/Y')),
        ];
    }


    protected function getDateRange(): array
    {
        $start = match ($this->filter) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfYear(),
        };

        return [$start, now()];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
