<?php /** @noinspection PhpIllegalArrayKeyTypeInspection */

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Expense;
use App\Models\Organisation;
use App\Models\Project;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Livewire\Component;

class ArchivesProject extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.archives-project');
    }

    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    public function table(Table $table): Table
    {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        return $table->query(
            Project::where('status', '=', -1)
                ->where(function ($query) {
                    $query->where('updated_at', '<=', now()->subYears(2))
                        ->orWhereJsonContains('deadlines->date', function ($subQuery) {
                            $subQuery->where('date', '<', now());
                        });
                }))->columns([
            IconColumn::make('status')
                ->label(false)
                ->boolean()
                ->trueIcon('heroicon-s-check-circle')
                ->trueColor('success')
                ->falseIcon('heroicon-s-x-circle')
                ->falseColor('danger')
                ->sortable()
                ->alignCenter(),
            BadgeableColumn::make('title')
                ->label('Programme')
                ->wrap()
                ->lineClamp(3)
                ->weight(FontWeight::SemiBold)
                ->sortable()
                ->suffixBadges(function (Project $record) {
                    // Conditionally add the badge only if the condition is met
                    if ($record->is_big) {
                        return [
                            Badge::make('is_big')
                                ->label('Projet majeur')
                                ->color('info')
                        ];
                    }

                    return [];
                })
                ->separator(false)
                ->searchable(),
            /*
            TextColumn::make('is_big')->badge()->label(false)->formatStateUsing(function ($state) {
                return $state == 1 ? 'Projet majeur' : null;
            })
                ->color(function ($state) {
                    return $state == 1 ? 'info' : 'secondary';
                }),

            TextColumn::make('deadline')
                ->label('Deadline 1')
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($record) {
                    if ($record->continuous) {
                        return 'Continue';
                    } elseif ($record->deadline == '0000-00-00 00:00:00') {
                        return 'N/A';
                    } else {
                        return \Carbon\Carbon::parse($record->deadline)->format('d/m/Y');
                    }
                }),
            TextColumn::make('deadline_2')
                ->label('Deadline 2')
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($record) {
                    if ($record->continuous_2) {
                        return 'Continue';
                    } elseif ($record->deadline_2 == '0000-00-00 00:00:00') {
                        return 'N/A';
                    } else {
                        return \Carbon\Carbon::parse($record->deadline_2)->format('d/m/Y');
                    }
                }),
            */
            TextColumn::make('first_deadline')
                ->label('Prochaine deadline'),
            TextColumn::make('organisations.title')
                ->label('Organisation')
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('short_description')
                ->label('Description courte')
                ->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state))
                ->wrap()
                ->lineClamp(2)
                ->limit(100),
            TextColumn::make('updated_at')
                ->label('Date de dernière modif.')
                ->dateTime('d/m/Y')
                ->sortable()
                ->alignCenter()
        ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->defaultSort('status', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->recordUrl(fn($record) => route('projects.show', $record->id))
            ->filters([
                Filter::make('is_big')->label('Projets majeurs')->query(fn($query) => $query->where('is_big', '=', 1)),
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
                            return $query->whereHas('organisations', function ($query) use ($organisationId) {
                                $query->where('organisation_id', $organisationId);
                            });
                        });
                    })
                    ->indicateUsing(fn($data) => isset($data['organisation_id']) ? 'Organisation : ' . Organisation::find($data['organisation_id'])->title : null),
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
            ]);
    }
}
