<?php

namespace App\Livewire;

use App\Models\InfoSession;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
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
        $actions = [];

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
            TextColumn::make('created_at')
                ->label('Type')
                ->formatStateUsing(function ($record) {
                    $location = $record['location'] ?? null;
                    $url = $record['url'] ?? null;

                    if ($location && $url) {
                        return "Hybride";
                    } elseif ($location) {
                        return "Présentiel";
                    } elseif ($url) {
                        return "Distanciel";
                    }

                    return 'Non spécifié';
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


        return $table->query(InfoSession::query())->columns($columns)->actions($actions);
    }
}
