<?php

namespace App\Livewire;

use App\Models\InfoSession;
use App\Models\Organisation;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ListInfoSession extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render()
    {
        return view('livewire.list-info-session');
    }

    public function table(Table $table): Table
    {
        $actions = [
            Action::make('edit')
                ->label('Modifier')
                ->icon('heroicon-o-pencil')
                ->action(fn($record) => redirect()->route('info_session.edit', $record->id))
                ->visible(fn($record) => auth()->check() && (
                        auth()->user()->can('edit others info_session') ||
                        auth()->user()->can('edit own info_session', $record)))
        ];

        $filters = [
            SelectFilter::make('session_type')
                ->label('Type.s de session')
                ->multiple()
                ->options([
                    2 => 'Hybride',
                    1 => 'Présentiel',
                    0 => 'Distanciel',
                ])
                ->attribute('session_type'),
            SelectFilter::make('organisation_id')
                ->label('Organisation.s')
                ->relationship('organisation', 'title')
                ->preload()
                ->multiple()
        ];

        $columns = [
            TextColumn::make('title')
                ->label('Titre')
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('description')
                ->label('Description')
                ->wrap()
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($record) {
                    return new HtmlString($record['description']);
                }),
            TextColumn::make('session_datetime')
                ->label('Date')
                ->wrap()
                ->sortable()
                ->searchable()
                ->dateTime('d/m/Y H:i'),
            TextColumn::make('session_type')
                ->label('Type')
                ->formatStateUsing(function ($record) {
                    switch ($record->session_type) {
                        case 0 :
                            return 'Distanciel';
                            break;
                        case 1 :
                            return 'Présentiel';
                            break;
                        case 2 :
                            return 'Hybride';
                            break;
                    }
                })
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('organisation.title')
                ->label('Organisation')
                ->wrap()
                ->sortable()
                ->searchable()
        ];


        return $table->query(InfoSession::query())->columns($columns)->actions($actions)->filters($filters)->recordUrl(fn($record) => route('info_session.show', $record->id));
    }
}
