<?php

namespace App\Livewire;

use App\Models\Draft;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Livewire\Component;

class ListDraft extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.list-draft');
    }

    public function table(Table $table): Table
    {
        $columns = [
            IconColumn::make('content.status')
                ->label(false)
                ->boolean()
                ->trueIcon('heroicon-s-check-circle')
                ->trueColor('success')
                ->falseIcon('heroicon-s-x-circle')
                ->falseColor('danger')
                ->sortable()
                ->alignCenter(),
            BadgeableColumn::make('content.title')
                ->label('Programme')
                ->wrap()
                ->lineClamp(3)
                ->weight(FontWeight::SemiBold)
                ->sortable()
                ->suffixBadges(function (Draft $record) {
                    if ($record->content['is_big'] ?? false) {
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
                ->label('Prochaine deadline'),
            TextColumn::make('content.organisations.title')
                ->label('Organisation')
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('content.short_description')
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

        if (Auth::check() && Auth::user()->can('create projects')) {
            $actions[] = Action::make('edit')
                ->label('Edit')
                //->url(fn($record) => route('projects.edit', $record->id))
                ->icon('heroicon-s-pencil')
                ->color('primary');

            $actions[] = Action::make('archive')
                ->label('Supprimer')
                ->icon('heroicon-s-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Supprimer le projet.')
                ->modalDescription('Voulez-vous vraiment supprimer ce projet ?.');
        }

        return $table->query(
            Draft::query()
        )
            ->columns($columns)
            ->actions($actions)
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100]);
        //->recordUrl(fn($record) => route('drafts.show', $record->id));
    }
}
