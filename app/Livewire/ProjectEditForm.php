<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Countries;
use App\Models\InfoType;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ScientificDomainCategory;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
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


class ProjectEditForm extends Component implements HasForms
{
    use InteractsWithForms;

    public $draft;
    public Project $project;
    public array $data = [];

    public function render()
    {
        return view('livewire.project-edit-form');
    }

    public function mount(Project $project)
    {
        $this->project = $project;

        $this->form->fill($this->project->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Tabs')->tabs([
                Tabs\Tab::make('Informations')->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->maxLength(255)
                        ->required()
                        ->autofocus(),
                    Select::make('organisation')
                        ->multiple()
                        ->createOptionForm([
                            TextInput::make('title')
                                ->required()
                        ])
                        ->label('Organisation')
                        ->required()
                        ->relationship(name: 'organisations', titleAttribute: 'title')
                        ->options(Organisation::all()->pluck('title', 'id')->toArray()),
                    Checkbox::make('is_big')
                        ->label('Projet majeur')
                        ->default(false),
                    Select::make('info')
                        ->label("Type d'information")
                        ->options([
                            'Financement',
                            "Séance d'information organisée par l'ULB",
                            "Séance d'information organisée par un organisme externe"
                        ])
                        ->selectablePlaceholder(false)
                        ->required(),
                    CheckboxList::make('info_types')
                        ->label('Types de programmes')
                        ->options(InfoType::all()->sortBy('title')->pluck('title')->toArray())
                        ->columns(3)
                        ->required(),
                    Select::make('Appel')
                        ->label("Disciplines scientifiques de l'appel")
                        ->multiple()
                        ->required()
                        ->options(function () {
                            $categories = ScientificDomainCategory::with('domains')->get();

                            $options = [];

                            foreach ($categories as $category) {
                                foreach ($category->domains as $domain) {
                                    $options[$category->name][$domain->id] = $domain->name;
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

                            $continents = Continent::all()->pluck('name', 'id')->toArray();
                            $pays = Countries::all()->pluck('nomPays', 'id')->toArray();

                            foreach ($continents as $id => $name) {
                                $options["continent_$id"] = $name;
                            }

                            foreach ($pays as $id => $name) {
                                $options["pays_$id"] = $name;
                            }

                            return $options;
                        }),

                ]),
                Tabs\Tab::make('Dates importantes')->schema([
                    Section::make('Deadlines')->schema([
                        Fieldset::make('1ere deadline')->schema([
                            DatePicker::make('deadline'),
                            TextInput::make('proof')
                                ->label('Justificatif'),
                            Checkbox::make('continuous')
                                ->label('Continu')
                                ->default(false)
                        ]),
                        Fieldset::make('2eme deadline')->schema([
                            DatePicker::make('deadline_2'),
                            TextInput::make('proof_2')
                                ->label('Justificatif'),
                            Checkbox::make('continuous_2')
                                ->label('Continu')
                                ->default(false)
                                ->inline(true)
                        ]),
                    ]),
                    Select::make('periodicity')
                        ->label('Periodicité')
                        ->options(['Sans', 'Annuel', 'Biennal', 'Triennal', 'Quadriennal', 'Quinquennal'])
                        ->selectablePlaceholder(false)
                        ->default(0),
                    DatePicker::make('date_lessor')
                        ->label('Date Bailleur'),
                ]),
                Tabs\Tab::make('Description')->schema([
                    Textarea::make('short_description')
                        ->label('Description courte')
                        ->maxLength(500)
                        ->hint(fn($state, $component) => strlen($state) . '/' . $component->getMaxLength())
                        ->live()
                        ->required(),
                    MarkdownEditor::make('long_description')
                        ->label('Description complète')
                        ->required(),
                    MarkdownEditor::make('funding')
                        ->label("Financement")
                        ->required(),
                ]),
                Tabs\Tab::make("Critères d'admission")->schema([
                    MarkdownEditor::make('admission_requirements')
                        ->label("")
                        ->required(),
                ]),
                Tabs\Tab::make("Pour postuler")->schema([
                    MarkdownEditor::make('apply_instructions')
                        ->label("")
                        ->required(),
                    FileUpload::make('docs')
                        ->multiple()
                        ->disk('public')
                        ->visibility('public')
                        ->directory('uploads/docs')
                ]),
                Tabs\Tab::make("Contacts")->schema([
                    Fieldset::make('Internes')->schema([
                        Repeater::make('contact_ulb')->schema([
                            TextInput::make('first_name')->label('Prénom'),
                            TextInput::make('last_name')->label('Nom'),
                            TextInput::make('email')->label('E-mail')->email(),
                            TextInput::make('tel')->label('Numéro de téléphone')->tel(),
                            TextInput::make('address')->label('Adresse')->columnSpan(2)
                        ])->columns(2)->addActionLabel('+ Nouveau contact')->label('')
                    ]),
                    Fieldset::make('Externes')->schema([
                        Repeater::make('contact_ext')->schema([
                            TextInput::make('first_name')->label('Prénom'),
                            TextInput::make('last_name')->label('Nom'),
                            TextInput::make('email')->label('E-mail')->email(),
                            TextInput::make('tel')->label('Numéro de téléphone')->tel(),
                            TextInput::make('address')->label('Adresse')->columnSpan(2)
                        ])->columns(2)->addActionLabel('+ Nouveau contact')
                    ]),
                ]),
            ]),
        ])->statePath('data')->model($this->project);
    }
}
