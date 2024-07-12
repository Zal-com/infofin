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
                TextColumn::make('content')
                    ->label('Title')
                    ->formatStateUsing(function ($state) {
                        $content = json_decode($state, true);
                        return $content['title'] ?? 'No Title'; // Accessing content.title
                    }),
            ])
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([5, 10, 25, 50, 100]);
    }

    public function render()
    {
        return view('livewire.user-drafts');
    }
}
