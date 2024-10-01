<?php

namespace App\Livewire;

use App\Models\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserCollection extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function render()
    {
        return view('livewire.user-collection');
    }

    public function copyLink($id)
    {
        // Envoyer une notification lorsque le lien est copié
        Notification::make()
            ->title('Lien copié dans le presse-papier.')
            ->icon('heroicon-o-check')
            ->color('success')
            ->iconColor('success')
            ->send();

        // Émettre un événement pour copier le lien côté client
        $link = url(route('collection.show', $id));
        $this->dispatch('copy-link', ['link' => $link]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(Collection::where('user_id', Auth::id()))
            ->columns([
                TextColumn::make('name')
                    ->label('Titre'),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap(),
                TextColumn::make('projects_count')
                    ->label('Nombre d\'appels')
            ])
            ->actions([
                Action::make('copy_link')
                    ->icon('heroicon-s-link')
                    ->iconButton()
                    ->label(false)
                    ->action(fn($record) => $this->copyLink($record->id))
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withCount('projects'));
    }
}
