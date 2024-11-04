<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Collection;
use App\Models\Expense;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\UserFavorite;
use App\Traits\ReplicateModelWithRelations;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;


class ListProjects extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use HasToggleableTable;
    use ReplicateModelWithRelations;

    protected $listeners = ['projectDeleted', 'refreshTable'];

    #[On('refreshTable')]
    public function render(): View
    {
        return view('livewire.list-projects');
    }

    public function table(Table $table): Table
    {

        $filters = [
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

        ];

        if (Auth::user()) {
            array_unshift($filters,
                Filter::make('is_big')->label('Projets majeurs')->query(fn($query) => $query->where('is_big', '=', 1)),
                Filter::make('pour_moi')
                    ->label('Pour moi')
                    ->query(function ($query) {
                        $user = Auth::user();
                        if ($user) {
                            $userInfoTypes = $user->info_types->pluck('id')->toArray();
                            $userScientificDomains = $user->scientific_domains->pluck('id')->toArray();

                            return $query->where(function ($query) use ($userInfoTypes, $userScientificDomains) {
                                $query->whereHas('info_types', function ($query) use ($userInfoTypes) {
                                    $query->whereIn('info_type_id', $userInfoTypes);
                                })->orWhereHas('scientific_domains', function ($query) use ($userScientificDomains) {
                                    $query->whereIn('scientific_domain_id', $userScientificDomains);
                                });
                            });
                        }
                        return $query;
                    }),
                Filter::make('favorites')
                    ->label('Favoris')
                    ->query(function ($query) {
                        $user = Auth::user();
                        if ($user) {
                            $favoriteProjectIds = UserFavorite::where('user_id', $user->id)
                                ->pluck('project_id')
                                ->toArray();

                            return $query->whereIn('id', $favoriteProjectIds);
                        }
                        return $query;
                    })

            );
        }

        return $table->query(
            Project::where('status', '!=', 2)->where('status', '!=', -1)
                ->where(function ($query) {
                    $query->where('updated_at', '>', now()->subYears(2))
                        ->orWhereJsonContains('deadlines->date', function ($subQuery) {
                            $subQuery->where('date', '>', now());
                        });
                }))
            ->columns(
                $this->isMobileLayout()
                    ? $this->getGridTableColumns()
                    : ($this->isGridLayout() ? $this->getGridTableColumns() : $this->getListTableColumns())
            )
            ->contentGrid(
                fn() => $this->isListLayout()
                    ? null
                    : [
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3,
                    ]
            )
            ->actions([
                Action::make('toggle_favorite')
                    ->label('Ajouter aux favoris')
                    ->icon(fn($record) => Auth::user()->favorites->contains($record->id) ? 'heroicon-s-bookmark' : 'heroicon-o-bookmark')
                    ->iconButton()
                    ->tooltip('Ajouter aux favoris')
                    ->color('black')
                    ->action(function ($record) {
                        $user = Auth::user();
                        $user->favorites->contains($record->id)
                            ? $user->removeFromFavorites($record->id)
                            : $user->addToFavorites($record->id);

                        $user->load('favorites');
                        $record->refresh();
                    })
                    ->visible(Auth::check()),
                Action::make('edit')
                    ->label('Modifier')
                    ->iconButton()
                    ->tooltip('Modifier')
                    ->url(fn($record) => route('projects.edit', $record->id))
                    ->icon('heroicon-s-pencil')
                    ->color('primary')
                    ->visible(Auth::check() && Auth::user()->can('create projects')),
                ActionGroup::make([
                    Action::make('add_to_collection')
                        ->label('Collection')
                        ->icon('heroicon-o-plus')
                        ->iconPosition('after')
                        ->modalHeading('Ajouter à une collection')
                        ->modalDescription('Choisissez une collection pour y ajouter cet appel.')
                        ->form([
                            Select::make('collection')
                                ->label('Collection')
                                ->options(Collection::where('user_id', Auth::id())->pluck('name', 'id')->toArray())
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('name')->label('Titre')->required(),
                                    TextInput::make('description')->label('Description')->maxLength(500),
                                ])
                                ->createOptionUsing(function ($data) {
                                    // Save the new collection to the database
                                    $collection = Collection::create([
                                        'name' => $data['name'],
                                        'description' => $data['description'],
                                        'user_id' => Auth::id(), // Assuming each collection belongs to a user
                                    ]);

                                    return $collection->id; // Return the ID of the newly created collection
                                }),
                        ])
                        ->action(function (array $data, $record) {
                            // Add the project to the selected collection

                            if ($record->collections()->where('collection_id', $data['collection'])->exists()) {
                                // Envoyer une notification si le projet est déjà dans la collection
                                Notification::make()
                                    ->title("Le projet est déjà dans cette collection.")
                                    ->icon('heroicon-o-information-circle')
                                    ->iconColor('warning')
                                    ->send();
                            } else {
                                // Ajouter le projet à la collection s'il n'est pas déjà présent
                                $record->collections()->attach($data['collection']);

                                Notification::make()
                                    ->title("Projet ajouté à la collection avec succès.")
                                    ->icon('heroicon-o-check-circle')
                                    ->iconColor('success')
                                    ->send();
                            }
                        }),
                    Action::make('archive')
                        ->label('Archiver')
                        ->icon('heroicon-o-archive-box')
                        ->iconPosition('after')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Supprimer le projet.')
                        ->modalDescription('Voulez-vous vraiment supprimer ce projet ?.')
                        ->action(function ($record) {
                            $record->update(['status' => -1]);
                            Notification::make()
                                ->title("Projet archivé avec succès")
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->color('success')
                                ->seconds(5)
                                ->send();
                        }),
                    Action::make('copyProject')
                        ->label('Dupliquer')
                        ->icon('heroicon-o-document-duplicate')
                        ->color("info")
                        ->action(function ($record) {
                            $project = $this->replicateModelWithRelations($record);

                            Notification::make()->title('Le projet a été copié avec succès.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();

                            return redirect()->route('projects.show', $project->id);
                        })
                ])
                    ->tooltip("Plus d'actions")
                    ->iconButton()
                    ->size('lg')
                    ->color('secondary')
                    ->visible(Auth::check() && Auth::user()->can('create projects'))])
            ->bulkActions([
                BulkAction::make('add_to_collection')
                    ->visible(Auth::check() && Auth::user()->hasRole(['contributor', 'admin']))
                    ->label('Ajouter à une collection')
                    ->icon('heroicon-o-plus')
                    ->iconPosition('before')
                    ->modalHeading('Collection')
                    ->form([
                        Select::make('collection')
                            ->options(Collection::where('user_id', Auth::id())->pluck('name', 'id')->toArray())
                            ->createOptionForm([
                                TextInput::make('name')->label('Titre')->required(),
                                TextInput::make('description')->label('Description')->maxLength(500),
                            ])
                            ->createOptionUsing(function ($data) {
                                // Create and return a new collection
                                $collection = Collection::create([
                                    'name' => $data['name'],
                                    'description' => $data['description'],
                                    'user_id' => Auth::id(),
                                ]);
                                return $collection->id; // Return the ID of the new collection
                            }),
                    ])
                    ->action(function (array $data, $records) {
                        // Handle the bulk action to add the selected records (projects) to the collection
                        $collection = Collection::findOrFail($data['collection']); // Get the selected collection

                        try {
                            // Attach each selected project to the collection
                            foreach ($records as $record) {
                                // Vérifier si le projet est déjà dans la collection
                                if (!$collection->projects()->where('project_id', $record->id)->exists()) {
                                    // Ajouter le projet uniquement s'il n'est pas déjà présent
                                    $collection->projects()->attach($record->id);
                                }
                            }

                            Notification::make()
                                ->title('Les projets ont été ajoutés à la collection avec succès')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->iconColor('success')
                                ->seconds(5)
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Quelque chose ne s\'est pas passé comme prévu. Veuillez réessayer.')
                                ->icon('heroicon-o-x-circle')
                                ->iconColor('danger')
                                ->color('danger')
                                ->seconds(5)
                                ->send();
                        }

                    })
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->recordUrl(fn($record) => route('projects.show', $record->id))
            ->filters($filters);
    }

    protected function getListTableColumns(): array
    {
        return [
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
                    if ($record->is_big && Auth::check() && Auth::user()->hasRole('contributor')) {
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
            TextColumn::make('firstDeadline')
                ->label('Prochaine deadline')
                ->formatStateUsing(function ($record) {
                    $deadline = explode('|', $record->firstDeadline);
                    return new HtmlString("
    <div>
        <p class='my-0'>{$deadline[0]}</p>
        <p class='text-gray-500 text-xs'>" . ($deadline[1] ?? '') . "</p>
    </div>
");
                }),
            TextColumn::make('organisation.title')
                ->label('Organisation')
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('short_description')
                ->label('Description courte')
                ->formatStateUsing(fn(string $state): HtmlString => new HtmlString(Markdown::parse($state)))
                ->wrap()
                ->lineClamp(2)
                ->limit(100),
            TextColumn::make('updated_at')
                ->label('Date de dernière modif.')
                ->dateTime('d/m/Y')
                ->sortable()
                ->alignCenter()
        ];
    }

    protected function getGridTableColumns(): array
    {
        return [
            Grid::make()->schema([
                Stack::make([
                    TextColumn::make('title')
                        ->label('Programme')
                        ->wrap()
                        ->lineClamp(3)
                        ->weight(FontWeight::SemiBold)
                        ->extraAttributes(['class' => 'text'], true)
                        ->sortable()
                        ->searchable()
                        ->columnSpanFull(),
                    TextColumn::make('organisation.title')
                        ->label('Organisation')
                        ->wrap()
                        ->extraAttributes(['class' => 'text-xs text-gray-500'])
                        ->sortable()
                        ->searchable()
                        ->columnSpanFull(),
                ])->columnSpan(5),
                IconColumn::make('status')
                    ->label("Actif")
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-s-x-circle')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter()
                    ->columnSpan(1),

                TextColumn::make('firstDeadline')
                    ->label(false)
                    ->formatStateUsing(fn() => 'Prochaine deadline : ')->columnSpan(3),
                Stack::make([
                    TextColumn::make('firstDeadline')
                        ->label(false)
                        ->formatStateUsing(fn($record) => explode('|', $record->firstDeadline)[0] ?? '')
                        ->alignEnd(),
                    TextColumn::make('firstDeadline')
                        ->label(false)
                        ->formatStateUsing(function ($record) {
                            $parts = explode('|', $record->firstDeadline);
                            if (!empty($parts[1])) {
                                return new HtmlString("<p class='text-sm text-gray-400' style='margin: 0; padding: 0;'>" . e(strip_tags($parts[1])) . "</p>");
                            }
                            return '';
                        })->extraAttributes(['style' => 'color: grey; font-size: 10'])
                        ->alignEnd(),
                ])->columnSpan(3)->alignEnd(),
                TextColumn::make('short_description')
                    ->label('Description courte')
                    ->formatStateUsing(fn(string $state): HtmlString => new HtmlString(Markdown::parse(strip_tags($state))))
                    ->wrap()
                    ->lineClamp(5)
                    ->columnSpanFull()->extraAttributes(['class' => 'text-justify']),
                TextColumn::make('updated_at')
                    ->label('Date de dernière modif.')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->alignCenter()
                    ->hidden()
            ])->columns(6)->extraAttributes(['class' => 'h-[350px]']),
        ];
    }

    private function isMobileLayout(): bool
    {
        return request()->header('User-Agent') && preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', request()->header('User-Agent'));
    }
}
