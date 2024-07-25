<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ProjectsChart extends ChartWidget
{
    protected static ?string $heading = 'Projets entrés dans Infofin';
    protected static string $color = 'info';
    
    protected function getData(): array
    {
        $data = Trend::model(Project::class)
            ->between(
                start: now()->startOfYear(),
                end: now())
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Projets entrés',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate)
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::make($value->date)->format('m/Y'))
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
