<?php

namespace App\Livewire;

use App\Models\Project;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\HtmlString;
use Livewire\Component;


class CollectionCreate extends Component implements Forms\Contracts\HasForms, Tables\Contracts\HasTable
{
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;

    public $name;
    public $description;
    public $selectedProjects = [];

    // Définition du schéma du formulaire
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nom')
                ->required(),

            Textarea::make('description')
                ->label('Description')
                ->required(),
        ];
    }

    // Définition des colonnes du tableau
    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('status')
                ->label(false)
                ->boolean()
                ->trueIcon('heroicon-s-check-circle')
                ->trueColor('success')
                ->falseIcon('heroicon-s-x-circle')
                ->falseColor('danger')
                ->sortable()
                ->alignCenter(),

            BadgeableColumn::make('title')
                ->label('Programme')
                ->wrap()
                ->sortable()
                ->searchable(),

            TextColumn::make('firstDeadline')
                ->label('Prochaine deadline')
                ->formatStateUsing(function ($record) {
                    $deadline = explode('|', $record->firstDeadline);
                    return new HtmlString("
                        <div>
                            <p class='my-0'>{$deadline[0]}</p>
                            <p class='text-gray-500 text-xs'>" . ($deadline[1] ?? '') . "</p>
                        </div>");
                }),

            TextColumn::make('organisation.title')
                ->label('Organisation')
                ->sortable()
                ->searchable(),

            TextColumn::make('updated_at')
                ->label('Date de dernière modif.')
                ->dateTime('d/m/Y')
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            // ...
        ];
    }

    public function submit()
    {
        dd($this->selectedProjects, $this->name, $this->description);
    }

    // Définir la requête pour le tableau
    protected function getTableQuery()
    {
        return Project::query()->where('status', 1); // Récupérer uniquement les projets où le statut est 1
    }

    public function render()
    {
        return view('livewire.collection-create');
    }
}
