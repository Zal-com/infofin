<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\InfoTypes;
use App\Models\Pays;
use App\Models\ScientificDomain;
use App\Models\ScientificDomainCategory;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

final class ProjectForm extends Component implements HasForms
{

    use InteractsWithForms;

    public array $data = [];

    public function mount(){
        $this->form->fill();
    }

    public function form(Form $form): Form {
        return $form->schema([
            TextInput::make('Name')
                ->label('Titre')
                ->maxLength(255)
                ->required(),
            TextInput::make('Organisation')
                ->label('Organisation')
                ->maxLength(255)
                ->required(),
            Fieldset::make('Deadlines')->
                schema([
                    Fieldset::make('1ere deadline')->schema([
                        DateTimePicker::make('deadline'),
                        Checkbox::make('Continu')
                            ->label('Continu')
                            ->default(False),
                        Select::make('Justificatif')
                            ->label('Justificatif')
                            ->options(['Draft', 'Version finale', 'more...'])
                        ]),
                    Fieldset::make('2eme deadline')->schema([
                        DateTimePicker::make('deadline2'),
                        Checkbox::make('Continu2')
                            ->label('Continu')
                            ->default(False),
                        Select::make('Justificatif2')
                            ->label('Justificatif')
                            ->options(['Draft', 'Version finale', 'more...'])
                    ]),
                ]),
            Select::make('Periodicity')
                ->label('Periodicité')
                ->options(['Sans', 'Annuel', 'Tous les deux ans']),
                DatePicker::make('DateBailleur')
                    ->label('Date Bailleur'),
                Checkbox::make('GProj')
                ->label('Projet majeur')
                ->default(False),
                Select::make('InfoType')
                    ->label("Type d'information")
                    ->options([
                        'Financement',
                        "Séance d'information organisée par l'ULB",
                        "Séance d'information organisée par un organisme externe"
                    ]),
                CheckboxList::make('Types')
                    ->label('Types de programmes')
                    ->options(InfoTypes::all()->pluck('Name')->toArray())
                    ->columns(3),
                Select::make('Appel')
                    ->label("Disciplines scientifiques de l'appel")
                    ->multiple()
                    ->options(function () {
                        $categories = ScientificDomainCategory::with('domains')->get();

                        $options = [];

                        foreach ($categories as $category) {
                            foreach ($category->domains as $domain) {
                                $options[$category->title][$domain->id] = $domain->title;
                            }
                        }
                        return $options;
                    }),
            Textarea::make('ShortDescription')
                ->label('Description courte')
                ->maxLength(500)
                ->hint(fn ($state, $component) => strlen($state) . '/' . $component->getMaxLength())
                ->live(),
            MarkdownEditor::make('LongDescription')
            ->label('Description complète'),
            MarkdownEditor::make('CriteresAdmission')
                ->label("Critères d'admission"),
            MarkdownEditor::make('Financement')
                ->label("Financement"),
            MarkdownEditor::make('PourPostuler')
                ->label("Pour postuler"),
            Select::make('Geo_zones')
                ->label("Zones géographiques")
                ->multiple()
                ->maxItems(3)
                ->options(function () {
                    $options = [
                        'Monde entier' => 'Monde entier',
                    ];
                    $options['Continents'] = Continent::all()->pluck('title', 'id')->toArray();
                    $options['Pays'] = Pays::all()->pluck('nomPays', )->toArray();
                    return $options;
                }),
            Fieldset::make('Contacts')->schema([
                Fieldset::make('Internes')->schema([
                    Repeater::make('Contact')->schema([
                        TextInput::make('first_name')->label('Prénom'),
                        TextInput::make('last_name')->label('Nom'),
                        TextInput::make('email')->label('E-mail')->email(),
                        TextInput::make('tel')->label('Numéro de téléphone')->tel(),
                    ])
                ]),
                Fieldset::make('Externes')->schema([
                    Repeater::make('Contact')->schema([
                        TextInput::make('first_name')->label('Prénom'),
                        TextInput::make('last_name')->label('Nom'),
                        TextInput::make('email')->label('E-mail')->email(),
                        TextInput::make('tel')->label('Numéro de téléphone')->tel(),
                    ])
                ]),
            ]),
        ])->statePath(path: 'data');
    }

    public function render()
    {
        return view('livewire.project-form');
    }
}
