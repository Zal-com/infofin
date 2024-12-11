<?php

namespace App\Livewire;

use App\Models\Collection;
use App\Models\Project;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Livewire\Component;

class CollectionEdit extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public ?array $data = [];
    public array $selectedTableRecords = [];
    public Collection $collection;

    public function mount(Collection $collection = null): void
    {
        $this->collection = $collection ?? new Collection();
        $this->selectedTableRecords = $this->collection->projects()->pluck('id')->map(function ($id) {
            return (string)$id;
        })->toArray();
        $this->form->fill($this->collection->toArray());
    }

    public function render()
    {
        return view('livewire.collection-edit');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::whereHas('collections', function ($query) {
                    $query->where('id', $this->collection->id);
                })
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
            ])
            ->bulkActions([
                BulkAction::make('remove_from_collection')
                    ->label('Retirer de la collection')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->action(function (\Illuminate\Support\Collection $records) {
                        try {
                            $this->selectedTableRecords = $records->pluck('id')->toArray();
                            $this->collection->projects()->detach($this->selectedTableRecords);
                            $this->dispatch('collection-updated');

                            Notification::make()
                                ->success()
                                ->icon('heroicon-o-check-circle')
                                ->title('Collection mise à jour.')
                                ->seconds(5)
                                ->send();
                        } catch (\Exception $exception) {
                            Notification::make()
                                ->danger()
                                ->icon('heroicon-o-x-circle')
                                ->title("Quelque chose ne s'est pas passé comme prévu. Veuillez réessayer.")
                                ->seconds(5)
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
            ])
            ->defaultPaginationPageOption(25);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Titre')
                            ->maxLength(255)
                            ->required(),
                        RichEditor::make('description')
                            ->label('Description')
                            ->toolbarButtons(['bold', 'bulletlist', 'italic', 'link', 'orderedList', 'strike', 'underline'])
                            ->maxLength(500)
                            ->nullable(),
                    ])
                    ->footerActions([
                        Action::make('submit')
                            ->icon('heroicon-o-check')
                            ->label('Valider')
                            ->action('submit')
                    ])
            ])
            ->model($this->collection)
            ->statePath('data');
    }

    public function submit(): void
    {

        if ($this->form->validate()) {
            try {
                $this->collection->update($this->data);
                Notification::make()
                    ->success()
                    ->icon('heroicon-o-check-circle')
                    ->title('Collection mise à jour.')
                    ->seconds(5)
                    ->send();
            } catch (\Exception $e) {
                Notification::make()
                    ->danger()
                    ->icon('heroicon-o-x-circle')
                    ->title("Quelque chose ne s'est pas passé comme prévu. Veuillez réessayer.")
                    ->seconds(5)
                    ->send();
            }
        }
    }
}
