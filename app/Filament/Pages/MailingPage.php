<?php

namespace App\Filament\Pages;

use App\Models\NewsletterSchedule;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

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
                ->label('Ajouter projet.s')
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
                        ->label('Selectionner un ou plusieurs projets')
                        ->multiple()
                        ->options(Project::all()->where('is_in_next_email', '!=', 1)->sortByDesc('id')->mapWithKeys(function ($project) {
                            return [$project->id => $project->id . ' - ' . $project->title];
                        })->toArray())->optionsLimit(0)
                        ->searchable()
                        ->required(),
                ])
                ->modalHeading('Ajout de projets au mail')
                ->modalWidth('lg'),

            Action::make('set_schedule')
                ->label('Définir le planning d\'envoi')
                ->icon('heroicon-s-calendar')
                ->action(function (array $data) {
                    try {
                        $schedule = NewsletterSchedule::first() ?: new NewsletterSchedule();
                        $schedule->day_of_week = $data['day_of_week'];
                        $schedule->send_time = $data['send_time'];
                        $schedule->save();

                        Notification::make()
                            ->title('Planning mis à jour avec succès.')
                            ->color('success')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Impossible de mettre à jour le planning. Veuillez réessayer.')
                            ->color('danger')
                            ->send();
                    }
                })
                ->form(function () {
                    $schedule = NewsletterSchedule::first();

                    return [
                        Select::make('day_of_week')
                            ->label('Jour de la semaine')
                            ->options([
                                '0' => 'Dimanche',
                                '1' => 'Lundi',
                                '2' => 'Mardi',
                                '3' => 'Mercredi',
                                '4' => 'Jeudi',
                                '5' => 'Vendredi',
                                '6' => 'Samedi',
                            ])
                            ->default($schedule?->day_of_week)
                            ->required(),
                        TimePicker::make('send_time')
                            ->seconds(false)
                            ->label('Heure d\'envoi')
                            ->default($schedule?->send_time)
                            ->required(),
                    ];
                })
                ->modalHeading('Définir le planning d\'envoi')
                ->modalWidth('lg'),
            Action::make('send_newsletter')
                ->label('Envoyer maintenant')
                ->icon('heroicon-s-paper-airplane')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        // Run the Artisan command
                        Artisan::call('newsletter:send');

                        Notification::make()
                            ->title('Newsletter envoyée avec succès.')
                            ->color('success')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Échec de l\'envoi de la newsletter. Veuillez réessayer.')
                            ->color('danger')
                            ->send();
                    }
                })
                ->modalWidth('lg')
        ];
    }
}
