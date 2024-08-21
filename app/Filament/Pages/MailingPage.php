<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MailingPage extends Page
{

    protected static ?string $navigationLabel = 'Mailing';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $activeNavigationIcon = 'heroicon-s-envelope';
    protected static string $view = 'filament.pages.mailing-page';

    public function getHeading(): string
    {
        return 'Mailing Management';
    }

    protected function getViewData(): array
    {
        return [
            'livewireComponent' => 'mailing-projects-table',
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('add')
                ->label('Ajouter un mail')
                ->icon('heroicon-s-plus')
                ->action(function (array $data) {
                    try {
                        foreach ($data['projects'] as $project) {
                            $project = Project::find($project);
                            $project->is_in_next_email = 1;
                            $project->save();
                        }
                        Notification::make()
                            ->title('Projets ajoutés avec succès.')
                            ->color('success')
                            ->seconds(5)
                            ->icon('heroicon-o-check-circle')
                            ->iconColor('success')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Impossible de rajouter les projets à la liste. Veuillez réessayer.')
                            ->color('danger')
                            ->seconds(5)
                            ->icon('heroicons-o-x-circle')
                            ->iconColor('danger')
                            ->send();
                    } finally {
                        $this->redirect(static::getUrl());
                    }

                })
                ->form([
                    Select::make('projects')
                        ->label('Select Projects')
                        ->multiple()
                        ->options(Project::all()->where('is_in_next_email', '!=', 1)->sortByDesc('id')->mapWithKeys(function ($project) {
                            return [$project->id => $project->id . ' - ' . $project->title];
                        })->toArray())->optionsLimit(0)
                        ->searchable()
                        ->required(),
                ])
                ->modalHeading('Select Projects')
                ->modalWidth('lg')
        ];
    }
}
