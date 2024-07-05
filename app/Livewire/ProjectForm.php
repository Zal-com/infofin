<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\InfoType;
use App\Models\Countries;
use App\Models\Organisation;
use App\Models\ScientificDomainCategory;
use App\Models\Project;
use Exception;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
                    Select::make('organisation_id')
                        ->createOptionForm([
                            TextInput::make('title')
                                ->required()
                        ])
                        ->label('Organisation')
                        ->required()
                        ->options(Organisation::all()->pluck('title', 'id')->toArray()),
                    Checkbox::make('is_big')
                        ->label('Projet majeur')
                        ->default(False),
                    Select::make('info')
                        ->label("Type d'information")
                        ->options([
                            'Financement',
                            "Séance d'information organisée par l'ULB",
                            "Séance d'information organisée par un organisme externe"
                        ])
                        ->selectablePlaceholder(false),
                    CheckboxList::make('info_types')
                        ->label('Types de programmes')
                        // ->options(InfoType::all()->sortBy('title')->pluck('title')->toArray())
                        ->options([
                            'Financement',
                            "Séance d'information organisée par l'ULB",
                            "Séance d'information organisée par un organisme externe"
                        ])
                        ->columns(3),
                    Select::make('Appel')
                        ->label("Disciplines scientifiques de l'appel")
                        ->multiple()
                        // ->options(function () {
                        //     $categories = ScientificDomainCategory::with('domains')->get();

                        //     $options = [];

                        //     foreach ($categories as $category) {
                        //         foreach ($category->domains as $domain) {
                        //             $options[$category->title][$domain->id] = $domain->title;
                        //         }
                        //     }
                        //     return $options;
                        // }),
                        ->options([
                            'Financement',
                            "Séance d'information organisée par l'ULB",
                            "Séance d'information organisée par un organisme externe"
                        ]),
                    Select::make('Geo_zones')
                        ->label("Zones géographiques")
                        ->multiple()
                        ->maxItems(3)
                        // ->options(function () {
                        //     $options = [
                        //         'Monde entier' => 'Monde entier',
                        //     ];
                        //     $options['Continents'] = Continent::all()->pluck('title', 'id')->toArray();
                        //     $options['Pays'] = Countries::all()->pluck('nomPays', 'codePays')->toArray();
                        //     return $options;
                        // }),
                        ->options(Countries::all()->pluck('nomPays', 'id')->toArray())
                ]),
                Tabs\Tab::make('Dates importantes')->schema([
                    Section::make('Deadlines')->
                    schema([
                        Fieldset::make('1ere deadline')->schema([
                            DateTimePicker::make('deadline'),
                            TextInput::make('proof')
                                ->label('Justificatif'),
                            Checkbox::make('continuous')
                                ->label('Continu')
                                ->default(False)
                                ->hint('Continu = jsp frr')
                        ]),
                        Fieldset::make('2eme deadline')->schema([
                            DateTimePicker::make('deadline_2'),
                            TextInput::make('proof_2')
                                ->label('Justificatif'),
                            Checkbox::make('continuous_2')
                                ->label('Continu')
                                ->default(False)
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
                        ->hint(fn ($state, $component) => strlen($state) . '/' . $component->getMaxLength())
                        ->live(),
                    MarkdownEditor::make('long_description')
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

    public function submit()
    {
        try{
            // Récupérer l'utilisateur connecté
            $userId = Auth::id();

            // Règles de validation
            // $rules = [
            //     'title' => 'required|string|max:255',
            //     'organisation_id' => 'required|exists:organisations,id',
            //     'is_big' => 'boolean',
            //     'InfoType' => 'required|string',
            //     'Types' => 'array',
            //     'Appel' => 'array',
            //     'Geo_zones' => 'array',
            //     'deadline' => 'nullable|date',
            //     'proof' => 'nullable|string|max:50',
            //     'continuous' => 'boolean',
            //     'deadline_2' => 'nullable|date',
            //     'proof_2' => 'nullable|string|max:50',
            //     'continuous_2' => 'boolean',
            //     'periodicity' => 'nullable|integer',
            //     'date_lessor' => 'nullable|date',
            //     'short_description' => 'nullable|string|max:500',
            //     'full_description' => 'nullable|string',
            //     'funding' => 'nullable|string',
            //     'admission_requirements' => 'nullable|string',
            //     'apply_instructions' => 'nullable|string',
            //     'contact_ulb.*.first_name' => 'nullable|string',
            //     'contact_ulb.*.last_name' => 'nullable|string',
            //     'contact_ulb.*.email' => 'nullable|email',
            //     'contact_ulb.*.tel' => 'nullable|string',
            //     'contact_ulb.*.address' => 'nullable|string',
            //     'contact_ext.*.first_name' => 'nullable|string',
            //     'contact_ext.*.last_name' => 'nullable|string',
            //     'contact_ext.*.email' => 'nullable|email',
            //     'contact_ext.*.tel' => 'nullable|string',
            //     'country_id' => 'required|exists:countries,id',
            //     'continent_id' => 'required|exists:continents,id',
            //     'status' => 'integer',
            //     'is_draft' => 'boolean',
            // ];

            $rules = [
                'title' => 'required|string|max:255',
                'is_big' => 'boolean',
                'Types' => 'array',
                'Appel' => 'array',
                'Geo_zones' => 'array',
                'deadline' => 'nullable|date',
                'proof' => 'nullable|string|max:50',
                'continuous' => 'boolean',
                'deadline_2' => 'nullable|date',
                'proof_2' => 'nullable|string|max:50',
                'continuous_2' => 'boolean',
                'periodicity' => 'nullable|integer',
                'date_lessor' => 'nullable|date',
                'short_description' => 'nullable|string|max:500',
                'long_description' => 'nullable|string',
                'funding' => 'nullable|string',
                'admission_requirements' => 'nullable|string',
                'apply_instructions' => 'nullable|string',
                'contact_ulb.*.first_name' => 'nullable|string',
                'contact_ulb.*.last_name' => 'nullable|string',
                'contact_ulb.*.email' => 'nullable|email',
                'contact_ulb.*.tel' => 'nullable|string',
                'contact_ulb.*.address' => 'nullable|string',
                'contact_ext.*.first_name' => 'nullable|string',
                'contact_ext.*.last_name' => 'nullable|string',
                'contact_ext.*.email' => 'nullable|email',
                'contact_ext.*.tel' => 'nullable|string',
                'status' => 'integer',
                'is_draft' => 'boolean',
            ];

            // Validation des données
            $validator = Validator::make($this->data, $rules);
            if ($validator->fails()) {
                $this->addError('validation', 'Validation Error');
                dd($validator);
                return;
            }

            $data = $validator->validated();

            // Ajouter les IDs de l'utilisateur connecté
            $data['poster_id'] = $userId;
            $data['last_update_user_id'] = $userId;

            // Traitement des contacts ULB
            $contactsUlB = [];
            foreach ($data['contact_ulb'] as $contact) {
                $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                $email = $contact['email'] ?? '';
                $phone = $contact['tel'] ?? '';
                $address = $contact['address'] ?? '';

                if ($name !== '' || $email !== '' || $phone !== '' || $address !== '') {
                    $contactsUlB[] = [
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                    ];
                }
            }
            $data['contact_ulb'] = !empty($contactsUlB) ? json_encode($contactsUlB) : '[]';

            // Traitement des contacts externes
            $contactsExt = [];
            foreach ($data['contact_ext'] as $contact) {
                $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                $email = $contact['email'] ?? '';
                $phone = $contact['tel'] ?? '';

                if ($name !== '' || $email !== '' || $phone !== '') {
                    $contactsExt[] = [
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                    ];
                }
            }
            $data['contact_ext'] = !empty($contactsExt) ? json_encode($contactsExt) : '[]';

            // Création du projet
            $project = Project::create($data);

            // Création des relations (commentées)
            /*
            // Attacher les domaines scientifiques
            if (!empty($data['Appel'])) {
                $project->scientificDomains()->attach($data['Appel']);
            }

            // Attacher les types d'information
            if (!empty($data['Types'])) {
                $project->info_types()->attach($data['Types']);
            }

            // Attacher les zones géographiques
            if (!empty($data['Geo_zones'])) {
                // Assuming you have a method or pivot table to handle geo zones
                $project->geoZones()->attach($data['Geo_zones']);
            }
            */

            // Debug pour vérifier les données
            dd($project);
        }catch(Exception $e){
            dd($e);
        }
    }
}
