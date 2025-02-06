<?php

namespace App\Livewire;

use App\Models\InfoSession;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Illuminate\Mail\Markdown;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
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
                ->label(false)
                ->icon('heroicon-s-pencil')
                ->iconButton()
                ->tooltip('Modifier')
                ->action(fn($record) => redirect()->route('info_session.edit', $record->id))
                ->visible(fn($record) => auth()->check() && (
                        auth()->user()->can('edit other info_session') ||
                        auth()->user()->can('edit own info_session', $record))),
            Action::make('delete')
                ->color('danger')
                ->label(false)
                ->tooltip('Supprimer')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action(fn($record) => $record->update(['status' => false]))
                ->visible(fn() => auth()->check() && auth()->user()->can('delete other info_session')) //perm session
                ->requiresConfirmation()
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
                ->weight(FontWeight::SemiBold)
                ->width('20%')
                ->wrap()
                ->lineClamp(2)
                ->sortable()
                ->searchable(),
            TextColumn::make('session_datetime')
                ->label('Date')
                ->lineClamp(2)
                ->wrap()
                ->sortable()
                ->searchable()
                ->description(fn($record) => Carbon::parse($record->session_datetime)->format('H:i'))
                ->dateTime('d/m/Y'),
            TextColumn::make('organisation.title')
                ->label('Organisation')
                ->wrap()
                ->lineClamp(2)
                ->sortable()
                ->width('20%')
                ->searchable(),
            TextColumn::make('description')
                ->label('Description')
                ->wrap()
                ->lineClamp(2)
                ->width('40%')
                ->sortable()
                ->searchable()
                ->formatStateUsing(fn(string $state): HtmlString => new HtmlString(Markdown::parse($state)))
                ->extraAttributes(['class' => 'info_session_description'])
                ->limit(200),
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
                ->searchable()
        ];

        return $table
            ->query(InfoSession::query()->where("session_datetime", ">", Carbon::now()))
            ->columns($columns)
            ->actions($actions)
            ->filters($filters)
            ->defaultSort('session_datetime', 'desc')
            ->recordUrl(fn($record) => route('info_session.show', $record->id));
    }
}
