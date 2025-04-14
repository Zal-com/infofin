<?php

namespace App\Livewire;

use App\Models\Collection;
use App\Models\InfoSession;
use Exception;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
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
                ->requiresConfirmation(),
            ActionGroup::make([
                Action::make('add_to_collection')
                    ->label('Collection')
                    ->icon('heroicon-o-plus')
                    ->iconPosition('after')
                    ->modalHeading('Ajouter à une collection')
                    ->modalDescription('Choisissez une collection pour y ajouter cet appel.')
                    ->form([
                        Select::make('collection')
                            ->label('Collection')
                            ->options(Collection::where('user_id', Auth::id())->pluck('name', 'id')->toArray())
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->label('Titre')->required(),
                                RichEditor::make('description')->label('Description')->maxLength(500)->toolbarButtons(['bold', 'bulletlist', 'italic', 'link', 'orderedList', 'strike', 'underline']),
                            ])->createOptionModalHeading('Créer une nouvelle collection')
                            ->createOptionUsing(function ($data) {
                                // Save the new collection to the database
                                $collection = Collection::create([
                                    'name' => $data['name'],
                                    'description' => $data['description'],
                                    'user_id' => Auth::id(), // Assuming each collection belongs to a user
                                ]);

                                return $collection->id; // Return the ID of the newly created collection
                            }),
                    ])
                    ->action(function (array $data, $record) {
                        // Add the project to the selected collection

                        if ($record->collections()->where('collection_id', $data['collection'])->exists()) {
                            // Envoyer une notification si le projet est déjà dans la collection
                            Notification::make()
                                ->title("La séance est déjà dans cette collection.")
                                ->icon('heroicon-o-information-circle')
                                ->iconColor('warning')
                                ->send();
                        } else {
                            // Ajouter le projet à la collection s'il n'est pas déjà présent
                            $record->collections()->attach($data['collection']);

                            Notification::make()
                                ->title("Séance ajoutée à la collection avec succès.")
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->send();
                        }
                    })
            ])
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
                ->lineClamp(2)
                ->sortable()
                ->searchable(),
            TextColumn::make('description')
                ->label('Description')
                ->wrap()
                ->lineClamp(2)
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
                ->lineClamp(2)
                ->sortable()
                ->searchable()
        ];

        return $table
            ->query(InfoSession::query()->where("status", "1"))
            ->columns($columns)
            ->actions($actions)
            ->filters($filters)
            ->defaultSort('session_datetime', 'desc')
            ->recordUrl(fn($record) => route('info_session.show', $record->id))
            ->bulkActions([
                BulkAction::make('add_to_collection')
                    ->deselectRecordsAfterCompletion()
                    ->visible(Auth::check() && Auth::user()->hasRole(['contributor', 'admin']))
                    ->label('Ajouter à une collection')
                    ->icon('heroicon-o-plus')
                    ->iconPosition('before')
                    ->modalHeading('Collection')
                    ->form([
                        Select::make('collection')
                            ->options(Collection::where('user_id', Auth::id())->pluck('name', 'id')->toArray())
                            ->createOptionForm([
                                TextInput::make('name')->label('Titre')->required(),
                                RichEditor::make('description')->label('Description')->maxLength(500)->toolbarButtons(['bold', 'bulletlist', 'italic', 'link', 'orderedList', 'strike', 'underline']),
                            ])->createOptionModalHeading('Créer une nouvelle collection')
                            ->createOptionUsing(function ($data) {
                                // Create and return a new collection
                                $collection = Collection::create([
                                    'name' => $data['name'],
                                    'description' => $data['description'],
                                    'user_id' => Auth::id(),
                                ]);
                                return $collection->id; // Return the ID of the new collection
                            }),
                    ])
                    ->action(function (array $data, $records) {
                        // Handle the bulk action to add the selected records (projects) to the collection
                        $collection = Collection::findOrFail($data['collection']); // Get the selected collection

                        try {
                            // Attach each selected project to the collection
                            foreach ($records as $record) {
                                // Vérifier si le projet est déjà dans la collection
                                if (!$collection->info_sessions()->where('info_session_id', $record->id)->exists()) {
                                    // Ajouter le projet uniquement s'il n'est pas déjà présent
                                    $collection->info_sessions()->attach($record->id);
                                }
                            }

                            Notification::make()
                                ->title('Les séances ont été ajoutés à la collection avec succès')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->iconColor('success')
                                ->seconds(5)
                                ->send();
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Quelque chose ne s\'est pas passé comme prévu. Veuillez réessayer.')
                                ->icon('heroicon-o-x-circle')
                                ->iconColor('danger')
                                ->color('danger')
                                ->seconds(5)
                                ->send();
                        }

                    })
            ]);
    }
}
