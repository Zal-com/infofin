<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Draft;
use App\Models\Expense;
use App\Models\Organisation;
use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function Livewire\wrap;

class UserProjects extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table->query(Project::where('poster_id', Auth::id())->where('status', 1))->columns([
            TextColumn::make('title')->label('Title')->limit(30)->lineClamp(2)->searchable()->sortable(),
            TextColumn::make('short_description')->label(__('Description courte'))->limit(50)->lineClamp(2)->searchable(),
            TextColumn::make('updated_at')->label(__('Date modif.'))->dateTime('d/m/Y H:i')->sortable(),
        ])
            ->recordUrl(fn(Project $record) => route('projects.show', $record->id))
            ->actions([
                Action::make('edit')
                    ->label('Modifier')
                    ->url(fn(project $record) => route('projects.edit', ['id' => $record->id]))
                    ->icon('heroicon-o-pencil')->iconPosition(IconPosition::Before),
                Action::make('delete')
                    ->label('Supprimer')
                    ->iconButton()
                    ->tooltip('Supprimer')
                    ->requiresConfirmation()
                    ->action(function (Project $project) {
                        try {
                            $project->update(['status' => '-1']);
                            Notification::make()
                                ->title('Projet supprimé avec succès.')
                                ->color('success')
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->seconds(5)
                                ->send();
                        } catch (\Exception $exception) {
                            Notification::make()
                                ->title('Impossible de supprimer le projet. Veuillez réessayer.')
                                ->color('danger')
                                ->icon('heroicon-o-x-circle')
                                ->iconColor('danger')
                                ->seconds(5)
                                ->send();
                        }

                    })
                    ->icon('heroicon-o-trash')->iconPosition(IconPosition::Before)
                    ->color('danger')

            ])->actionsPosition(ActionsPosition::AfterColumns)
            ->filters([
                    Filter::make('organisation')->label('Organisation')->form([
                        Select::make('organisation_id')
                            ->label('Organisation')
                            ->options(function () {
                                return Organisation::query()->pluck('title', 'id')->toArray();
                            })
                            ->searchable()
                    ])
                        ->query(function ($query, $data) {
                            return $query->when($data['organisation_id'], function ($query, $organisationId) {
                                return $query->where('organisation_id', $organisationId);
                            });
                        })
                        ->indicateUsing(fn($data) => isset($data['organisation_id']) ? 'Organisation : ' . Organisation::find($data['organisation_id'])->title : null),
                    Filter::make('scientific_domain')
                        ->label('Disciplines scientifiques')
                        ->form([
                            Select::make('scientific_domains') // "scientific_domains" doit être le nom du champ
                            ->label('Disciplines scientifiques')
                                ->relationship('scientific_domains', 'name') // On garde la relation
                                ->multiple()
                                ->preload()
                        ])
                        ->query(function ($query, $data) {
                            if (!empty($data['scientific_domains'])) {
                                return $query->whereHas('scientific_domains', function ($query) use ($data) {
                                    // On vérifie les IDs retournés par la sélection multiple
                                    $query->whereIn('scientific_domains.id', $data['scientific_domains']);
                                });
                            }
                        })
                        ->indicateUsing(function ($data) {
                            // Indiquer le filtre uniquement si des disciplines sont sélectionnées
                            if (!empty($data['scientific_domains'])) {
                                $selectedDomains = \App\Models\ScientificDomain::whereIn('id', $data['scientific_domains'])->pluck('name')->toArray();
                                return
                                    'Disciplines scientifiques : ' . implode(', ', $selectedDomains);
                            }
                            return null;
                        }),
                    Filter::make('activity_expense')
                        ->label('Filtrer par Activités et Dépenses')
                        ->form([
                            Select::make('activity_id')
                                ->label('Activités')
                                ->multiple()
                                ->options(function () {
                                    return Activity::all()->pluck('title', 'id')->toArray();
                                }),
                            Select::make('expense_id')
                                ->label('Dépenses')
                                ->multiple()
                                ->options(function () {
                                    return Expense::all()->pluck('title', 'id')->toArray();
                                }),
                        ])
                        ->query(function ($query, $data) {
                            if (!empty($data['activity_id']) || !empty($data['expense_id'])) {
                                return $query->where(function ($subQuery) use ($data) {
                                    if (!empty($data['activity_id'])) {
                                        $subQuery->whereHas('activities', function ($q) use ($data) {
                                            $q->whereIn('activity_id', $data['activity_id']);
                                        });
                                    }

                                    if (!empty($data['expense_id'])) {
                                        $subQuery->orWhereHas('expenses', function ($q) use ($data) {
                                            $q->whereIn('expense_id', $data['expense_id']);
                                        });
                                    }
                                });
                            }
                            return $query;
                        })
                        ->indicateUsing(function ($data) {
                            $indicators = [];

                            if (isset($data['activity_id']) && !empty($data['activity_id'])) {
                                $activityNames = Activity::whereIn('id', $data['activity_id'])->pluck('title')->toArray();
                                $indicators[] = 'Activités : ' . implode(', ', $activityNames);
                            }

                            if (isset($data['expense_id']) && !empty($data['expense_id'])) {
                                $expenseNames = Expense::whereIn('id', $data['expense_id'])->pluck('title')->toArray();
                                $indicators[] = 'Dépenses : ' . implode(', ', $expenseNames);
                            }

                            return implode(' | ', $indicators);
                        })

                ]
            );
    }

    public function render()
    {
        return view('livewire.user-projects');
    }
}
