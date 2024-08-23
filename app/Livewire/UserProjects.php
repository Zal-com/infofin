<?php

namespace App\Livewire;

use App\Models\Draft;
use App\Models\Project;
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

class UserProjects extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table->query(Project::where('poster_id', Auth::id())->where('status', 1))->columns([
            TextColumn::make('title')->label('Title'),
            TextColumn::make('short_description')->label(__('Description courte')),
            TextColumn::make('updated_at')->label(__('Date modif.'))->dateTime('d/m/Y H:i'),
        ])
            ->recordUrl(fn(Project $record) => route('projects.show', $record->id))
            ->actions([
                Action::make('edit')
                    ->label('Modifier')
                    ->url(fn(project $record) => route('projects.edit', ['id' => $record->id]))
                    ->icon('heroicon-o-pencil-square')->iconPosition(IconPosition::Before),
                Action::make('delete')
                    ->label('Supprimer')
                    ->requiresConfirmation()
                    ->action(function (Project $project) {
                        try {
                            $project->update(['status' => '-1']);
                            Notification::make()
                                ->title('Projet supprimé avec succès.')
                                ->color('success')
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->seconds(5)
                                ->send();
                        } catch (\Exception $exception) {
                            Notification::make()
                                ->title('Impossible de supprimer le projet. Veuillez réessayer.')
                                ->color('danger')
                                ->icon('heroicon-o-x-circle')
                                ->iconColor('danger')
                                ->seconds(5)
                                ->send();
                        }

                    })
                    ->icon('heroicon-o-trash')->iconPosition(IconPosition::Before)
                    ->color('danger')

            ])->actionsPosition(ActionsPosition::BeforeColumns);
    }

    public function render()
    {
        return view('livewire.user-projects');
    }
}
