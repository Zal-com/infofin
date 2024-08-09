<?php

namespace App\Livewire;

use App\Models\Draft;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
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
                TextColumn::make('content.title')
                    ->label('Titre'),
                TextColumn::make('content.short_description')
                    ->label('Description'),
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Date de modif.')
            ])
            ->actions([
                Action::make('edit')
                    ->label('Modifier')
                    ->url(fn(Draft $record) => route('projects.create', ['record' => $record->id]))
                    ->icon('heroicon-o-pencil-square')->iconPosition(IconPosition::Before),
                Action::make('delete')
                    ->label('Supprimer')
                    ->requiresConfirmation()
                    ->action(function (Draft $draft) {
                        $draft->delete();
                        Notification::make()->title('Brouillon supprimé.')
                            ->icon('heroicon-o-check-circle')
                            ->iconColor('success')
                            ->seconds(5)
                            ->send();
                    })
                    ->icon('heroicon-o-trash')->iconPosition(IconPosition::Before)
                    ->color('danger')

            ])->actionsPosition(ActionsPosition::BeforeColumns)
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->recordUrl(fn($record) => route('projects.create', ['record' => $record->id]));
    }

    public function render()
    {
        return view('livewire.user-drafts');
    }
}
