<?php

namespace App\Livewire;

use App\Models\Draft;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDrafts extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table->query(Draft::where('poster_id', Auth::id()))
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->getStateUsing(function ($record) {
                        $content = json_decode($record->content, true);
                        return $content['title'] ?? 'N/A';
                    }),
                TextColumn::make('short_description')
                    ->label('Description')
                    ->getStateUsing(function ($record) {
                        $content = json_decode($record->content, true);
                        return $content['short_description'] ?? 'N/A';
                    }),
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Date de modif.')
            ])
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->recordUrl(fn($record) => route('projects.create', $record->id));
    }

    public function render()
    {
        return view('livewire.user-drafts');
    }
}
