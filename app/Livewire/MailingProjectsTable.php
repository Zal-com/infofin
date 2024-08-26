<?php

namespace App\Livewire;

use App\Models\NewsletterSchedule;
use App\Models\Project;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Carbon\Carbon;
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

class MailingProjectsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public NewsletterSchedule $schedule;

    public function mount()
    {
        $this->schedule = NewsletterSchedule::first() ?? new NewsletterSchedule();
        if (!is_null($this->schedule->day_of_week)) {
            $this->schedule->day_of_week = Carbon::create()
                ->startOfWeek()
                ->addDays($this->schedule->day_of_week - 1)
                ->getTranslatedDayName();
        }

        if (!is_null($this->schedule->send_time)) {
            $this->schedule->send_time = Carbon::createFromFormat('H:i:s', $this->schedule->send_time)->format('H:i');
        }
    }

    public function table(Table $table): Table
    {
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
                        $record->update(['is_in_next_email' => -1]);
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
            Project::where('is_in_next_email', 1)
                //Ne pas prendre les projets archivés
                ->where(function ($query) {
                    $query->where('updated_at', '>', now()->subYears(2))
                        ->orWhereJsonContains('deadlines->date', function ($subQuery) {
                            $subQuery->where('date', '>', now());
                        });
                }))
            ->columns($columns)
            ->actions($actions)
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->recordUrl(fn($record) => route('projects.show', $record->id));
    }
}
