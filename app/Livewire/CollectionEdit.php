<?php

namespace App\Livewire;

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
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CollectionEdit extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public ?array $data = [];
    public $collection;

    public function mount(Collection $collection): void
    {
        $this->collection = $collection;
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
                TextColumn::make('title'),
            ])
            ->bulkActions([
                BulkAction::make('add_to_collection')
                    ->label('Ajouter')
                    ->icon('heroicon-o-plus')
                    ->action(function (Collection $records) {
                        //
                    })
            ]);
        //idk
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
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();

        // Logique pour sauvegarder la collection
        // Par exemple : Collection::create($data);

        // Redirection ou message de succès
    }
}
