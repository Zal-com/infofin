<?php

namespace App\Livewire;

use App\Models\InfoTypeCategory;
use App\Models\Organisation;
use App\Models\Project;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Concerns\CanPersistTab;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use FilamentTiptapEditor\Extensions\Nodes\GridColumn;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Hydrat\TableLayoutToggle\Contracts\LayoutPersister;
use Hydrat\TableLayoutToggle\Persisters\LocalStoragePersister;
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

    protected $listeners = ['projectDeleted', 'refreshTable'];

    #[On('refreshTable')]
    public function render(): View
    {
        return view('livewire.list-projects');
    }

    #[On('projectDeleted')]
    public function projectDeleted()
    {
        Notification::make()
            ->title("Projet supprimé avec succès")
            ->icon('heroicon-o-x-circle')
            ->iconColor('danger')
            ->color('danger')
            ->seconds(5)
            ->send();

        return redirect()->route('projects.index');
    }

    public function table(Table $table): Table
    {
        $columns = [
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
        ];

        $actions = [];

        $filters = [
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
            Filter::make('info_type_category')
                ->label('Catégories')
                ->form([
                    Select::make('category_id')
                        ->label('Categorie')
                        ->multiple()
                        ->options(function () {
                            return InfoTypeCategory::all()->pluck('name', 'id')->toArray();
                        })
                ])
                ->query(function ($query, $data) {
                    if (!empty($data['category_id'])) {
                        return $query->whereHas('info_types', function ($query) use ($data) {
                            $query->whereIn('info_types_cat_id', $data['category_id']);
                        });
                    }
                    return $query;
                })
                ->indicateUsing(function ($data) {
                    if (isset($data['category_id']) && !empty($data['category_id'])) {
                        $categoryNames = InfoTypeCategory::whereIn('id', $data['category_id'])->pluck('name')->toArray();
                        return 'Catégories : ' . implode(', ', $categoryNames);
                    }
                    return null;
                })
        ];

        if (Auth::check()) {
            if (Auth::user()->can('create projects')) {
                $actions[] = Action::make('edit')
                    ->label('Edit')
                    ->url(fn($record) => route('projects.edit', $record->id))
                    ->icon('heroicon-s-pencil')
                    ->color('primary');

                $actions[] = Action::make('archive')
                    ->label('Supprimer')
                    ->icon('heroicon-s-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer le projet.')
                    ->modalDescription('Voulez-vous vraiment supprimer ce projet ?.')
                    ->action(function ($record) {
                        $record->update(['status' => -1]);
                        $this->dispatch("projectDeleted");
                    });

            }

            $actions[] =
                Action::make('toggle_favorite')
                    ->label(false)
                    ->icon(fn($record) => Auth::user()->favorites->contains($record->id) ? 'heroicon-s-bookmark' : 'heroicon-o-bookmark')
                    ->iconButton()
                    ->color('black')
                    ->action(function ($record) {
                        $user = Auth::user();
                        $user->favorites->contains($record->id)
                            ? $user->removeFromFavorites($record->id)
                            : $user->addToFavorites($record->id);

                        $user->load('favorites');
                        $record->refresh();
                    });

            $filters[] = Filter::make('pour_moi')
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
                });
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
                $this->isGridLayout()
                    ? $this->getGridTableColumns()
                    : $this->getListTableColumns()
            )
            ->contentGrid(
                fn() => $this->isListLayout()
                    ? null
                    : [
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ]

            )
            ->actions($actions)->actionsAlignment('end')
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
        ];
    }

    protected function getGridTableColumns(): array
    {
        return [
            Grid::make()->schema([
                Stack::make([
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
                        ->searchable()
                        ->columnSpan(4),
                    TextColumn::make('organisations.title')
                        ->label('Organisation')
                        ->wrap()
                        ->sortable()
                        ->searchable()
                        ->columnSpan(4),
                ])->columnSpanFull(),

                TextColumn::make('firstDeadline')
                    ->label(false)
                    ->formatStateUsing(fn() => 'Prochaine deadline : ')->columnSpan(2),
                Stack::make([
                    TextColumn::make('firstDeadline')
                        ->label(false)
                        ->formatStateUsing(fn($record) => explode('|', $record->firstDeadline)[0] ?? '')
                        ->alignEnd(),
                    TextColumn::make('firstDeadline')
                        ->label(false)
                        ->formatStateUsing(function ($record) {
                            $parts = explode('|', $record->firstDeadline);
                            if (isset($parts[1]) && !empty($parts[1])) {
                                return new HtmlString("<p class='text-sm text-gray-400'>" . e($parts[1]) . "</p>");
                            }
                            return '';
                        })->extraAttributes(['style' => 'color: grey; font-size: 10'])
                        ->alignEnd(),
                ])->columnSpan(2)->alignEnd(),
                TextColumn::make('short_description')
                    ->label('Description courte')
                    ->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state))
                    ->wrap()
                    ->lineClamp(5)
                    ->columnSpan(4)->extraAttributes(['class' => 'text-justify']),
                TextColumn::make('updated_at')
                    ->label('Date de dernière modif.')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->alignCenter()
                    ->hidden()
            ])->columns(4)
        ];
    }

}
