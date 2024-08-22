<?php

namespace App\Livewire;

use App\Models\NewsletterSchedule;
use App\Models\Project;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Carbon\Carbon;

class MailingProjectsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        $newsletter = NewsletterSchedule::get()->first();

        if ($newsletter && is_numeric($newsletter->day_of_week) && $newsletter->day_of_week >= 0 && $newsletter->day_of_week <= 6) {
            $currentDayOfWeek = Carbon::now()->dayOfWeek;

            $daysToAdd = ($newsletter->day_of_week - $currentDayOfWeek + 7) % 7;
            if ($daysToAdd === 0) {
                $daysToAdd = 7;
            }

            $nextScheduledDate = Carbon::now()
                ->addDays($daysToAdd)
                ->setTimeFromTimeString($newsletter->send_time)
                ->locale('fr')
                ->isoFormat('dddd D MMMM YYYY HH:mm');

            $headers = "Prochain envoi prévu " . $nextScheduledDate;
        } else {
            $headers = "Prochain envoi prévu : Informations indisponibles";
        }

        $columns = [
            TextColumn::make("id")
                ->searchable()
                ->sortable(),
            BadgeableColumn::make('title')
                ->label('Programme')
                ->wrap()
                ->lineClamp(3)
                ->weight(FontWeight::SemiBold)
                ->sortable()
                ->suffixBadges(function (Project $record) {
                    if ($record->is_big) {
                        return [
                            Badge::make('is_big')
                                ->label('Projet majeur')
                                ->color('primary')
                        ];
                    }
                    return [];
                })
                ->separator(false)
                ->searchable(),
        ];

        $actions = [
            Action::make('remove')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->label('Supprimer')
                ->action(function ($record) {
                    try {
                        $record->update(['is_in_next_email' => 0]);
                        Notification::make()
                            ->title('Projet retiré de la liste avec succès.')
                            ->color('success')
                            ->seconds(5)
                            ->icon('heroicon-o-check-circle')
                            ->iconColor('success')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Impossible de retirer le projet de la liste. Veuillez réessayer')
                            ->color('danger')
                            ->seconds(5)
                            ->icon('heroicon-o-x-circle')
                            ->iconColor('danger')
                            ->send();
                    }

                })
            ,
        ];

        return $table->query(
            Project::where('status', '!=', 2)
                ->where('status', '!=', -1)
                ->where('is_in_next_email', 1)
                ->where(function ($query) {
                    $query->where('updated_at', '>', now()->subYears(2))
                        ->orWhereJsonContains('deadlines->date', function ($subQuery) {
                            $subQuery->where('date', '>', now());
                        });
                }))
            ->heading($headers)
            ->columns($columns)
            ->actions($actions)
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->recordUrl(fn($record) => route('projects.show', $record->id));
    }
}
