<?php

namespace App\Filament\Pages;

use App\Models\NewsletterSchedule;
use App\Models\Project;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class MailingPage extends Page
{
    protected static ?string $navigationLabel = 'Newsletter';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $activeNavigationIcon = 'heroicon-s-envelope';
    protected static string $view = 'filament.pages.mailing-page';

    public function getHeading(): string
    {
        return 'Gestion de la newsletter';
    }

    protected function getViewData(): array
    {
        return [
            'livewireComponent' => 'mailing-projects-table',
            'livewireComponent' => 'begin-mail'
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('add')
                ->label('Ajouter projets')
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
                    } catch (Exception $e) {
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
                        })->toArray())
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
                    } catch (Exception $e) {
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
                        Select::make('send_time')
                            ->options([
                                    '00:00',
                                    '00:30',
                                    '01:00',
                                    '01:30',
                                    '02:00',
                                    '02:30',
                                    '03:00',
                                    '03:30',
                                    '04:00',
                                    '04:30',
                                    '05:00',
                                    '05:30',
                                    '06:00',
                                    '06:30',
                                    '07:00',
                                    '07:30',
                                    '08:00',
                                    '08:30',
                                    '09:00',
                                    '09:30',
                                    '10:00',
                                    '10:30',
                                    '11:00',
                                    '11:30',
                                    '12:00',
                                    '12:30',
                                    '13:00',
                                    '13:30',
                                    '14:00',
                                    '14:30',
                                    '15:00',
                                    '15:30',
                                    '16:00',
                                    '16:30',
                                    '17:00',
                                    '17:30',
                                    '18:00',
                                    '18:30',
                                    '19:00',
                                    '19:30',
                                    '20:00',
                                    '20:30',
                                    '21:00',
                                    '21:30',
                                    '22:00',
                                    '22:30',
                                    '23:00',
                                    '23:30',
                                ]
                            )
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
                    } catch (Exception $e) {
                        Notification::make()
                            ->title('Échec de l\'envoi de la newsletter. Veuillez réessayer.')
                            ->color('danger')
                            ->send();
                    }
                })
                ->modalWidth('lg'),
            Action::make('send_previsu')
                ->label('Prévisualisation')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->action(function () {
                    try {
                        // Run the Artisan command
                        Artisan::call('newsletter:previsu');

                        Notification::make()
                            ->title('Prévisualisation envoyée avec succès.')
                            ->color('success')
                            ->send();
                    } catch (Exception $e) {
                        Notification::make()
                            ->title('Échec de l\'envoi de la prévisualisation. Veuillez réessayer.')
                            ->color('danger')
                            ->send();
                    }
                })
                ->modalWidth('lg')
        ];
    }
}
