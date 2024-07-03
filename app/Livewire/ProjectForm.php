<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\InfoType;
use App\Models\Countries;
use App\Models\ScientificDomainCategory;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
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
            Tabs::make('Tabs')->tabs([
                Tabs\Tab::make('Informations')->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->maxLength(255)
                        ->required()
                        ->autofocus(),
                    TextInput::make('organisation_id')
                        ->label('Organisation')
                        ->maxLength(255)
                        ->required(),
                    Checkbox::make('is_big')
                        ->label('Projet majeur')
                        ->default(False),
                    Select::make('InfoType')
                        ->label("Type d'information")
                        ->options([
                            'Financement',
                            "Séance d'information organisée par l'ULB",
                            "Séance d'information organisée par un organisme externe"
                        ])
                        ->selectablePlaceholder(false),
                    CheckboxList::make('Types')
                        ->label('Types de programmes')
                        ->options(InfoType::all()->sortBy('title')->pluck('title')->toArray())
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
                    Select::make('Geo_zones')
                        ->label("Zones géographiques")
                        ->multiple()
                        ->maxItems(3)
                        ->options(function () {
                            $options = [
                                'Monde entier' => 'Monde entier',
                            ];
                            $options['Continents'] = Continent::all()->pluck('title', 'id')->toArray();
                            $options['Pays'] = Countries::all()->pluck('nomPays', 'codePays')->toArray();
                            return $options;
                        }),
                ]),
                Tabs\Tab::make('Dates importantes')->schema([
                    Section::make('Deadlines')->
                    schema([
                        Fieldset::make('1ere deadline')->schema([
                            DateTimePicker::make('deadline'),
                            Select::make('proof')
                                ->label('Justificatif')
                                ->options(['Draft', 'Version finale', 'more...']),
                            Checkbox::make('continuous')
                                ->label('Continu')
                                ->default(False)
                                ->hint('Continu = jsp frr')
                        ]),
                        Fieldset::make('2eme deadline')->schema([
                            DateTimePicker::make('deadline_2'),
                            Select::make('proof_2')
                                ->label('Justificatif')
                                ->options(['Draft', 'Version finale', 'more...']),
                            Checkbox::make('continuous_2')
                                ->label('Continu')
                                ->default(False)
                                ->inline(true)
                        ]),
                    ]),
                    Select::make('periodicity')
                        ->label('Periodicité')
                        ->options(['Sans', 'Annuel', 'Biennal', 'Triennal', 'Quadriennal', 'Quinquennal'])
                        ->selectablePlaceholder(false),
                    DatePicker::make('date_lessor')
                        ->label('Date Bailleur'),
                ]),
                Tabs\Tab::make('Description')->schema([
                    Textarea::make('short_description')
                        ->label('Description courte')
                        ->maxLength(500)
                        ->hint(fn ($state, $component) => strlen($state) . '/' . $component->getMaxLength())
                        ->live(),
                    MarkdownEditor::make('full_description')
                        ->label('Description complète'),
                    MarkdownEditor::make('funding')
                        ->label("Financement"),
                ]),
                Tabs\Tab::make("Critères d'admission")->schema([
                    MarkdownEditor::make('admission_requirements')
                        ->label(""),
                ]),
                Tabs\Tab::make("Pour postuler")->schema([
                    MarkdownEditor::make('apply_instructions')
                        ->label(""),
                ]),
                Tabs\Tab::make("Contacts")->schema([
                    Fieldset::make('Internes')->schema([
                        Repeater::make('contact_ulb')->schema([
                            TextInput::make('first_name')->label('Prénom'),
                            TextInput::make('last_name')->label('Nom'),
                            TextInput::make('email')->label('E-mail')->email(),
                            TextInput::make('tel')->label('Numéro de téléphone')->tel(),
                            TextInput::make('address')->label('Adresse')->columnSpan(2)
                        ])->columns(2)
                    ]),
                    Fieldset::make('Externes')->schema([
                        Repeater::make('contact_ext')->schema([
                            TextInput::make('first_name')->label('Prénom'),
                            TextInput::make('last_name')->label('Nom'),
                            TextInput::make('email')->label('E-mail')->email(),
                            TextInput::make('tel')->label('Numéro de téléphone')->tel(),
                        ])->columns(2)->addActionLabel('+ Nouveau contact')
                    ]),
                ]),
            ]),
        ])->statePath(path: 'data');
    }

    public function render()
    {
        return view('livewire.project-form');
    }
}
