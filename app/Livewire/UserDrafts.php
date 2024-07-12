<?php

namespace App\Livewire;

use App\Models\Draft;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDrafts extends Component
{
    public function table(Table $table): Table
    {
        return $table->query(Draft::where('poster_id', Auth::id()))
            ->columns([
                TextColumn::make('content'),
            ])
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([5, 10, 25, 50, 100]);
    }

    public function render()
    {
        return view('livewire.user-drafts');
    }
}
