<?php

namespace App\Livewire;

use App\Models\Collection;
use App\Models\Project;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class CollectionEdit extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public ?array $data = [];
    public array $selectedProjects = [];
    public Collection $collection;

    public function mount(Collection $collection = null): void
    {
        $this->collection = $collection ?? new Collection();
        $this->selectedProjects = $this->collection->projects->pluck('id')->toArray();
        $this->form->fill($this->collection->toArray());
    }

    public function render()
    {
        return view('livewire.collection-edit');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query())
            ->columns([
                TextColumn::make('title')
                    ->label('Titre'),
            ])
            ->bulkActions([
                BulkAction::make('update_collection')
                    ->label('Mettre à jour la collection')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Collection $records) {
                        $this->selectedProjects = $records->pluck('id')->toArray();
                        $this->collection->projects()->sync($this->selectedProjects);
                        $this->dispatch('collection-updated');
                    })
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
            ])
            ->defaultPaginationPageOption(25)
            ->modifyQueryUsing(fn(Builder $query) => $query->orderBy('title'))
            ->defaultSort('title', 'asc');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Titre')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('description')
                    ->label('Description')
                    ->maxLength(500)
                    ->nullable(),
            ])
            ->model($this->collection)
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();

        $this->collection->update($data);
        $this->collection->projects()->sync($this->selectedProjects);

        $this->dispatch('collection-saved');
    }

    public function getListeners()
    {
        return [
            'collection-updated' => '$refresh',
            'collection-saved' => '$refresh',
        ];
    }
}
