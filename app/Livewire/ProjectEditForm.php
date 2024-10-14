<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Document;
use App\Models\Draft;
use App\Models\Expense;
use App\Models\InfoSession;
use App\Models\Project;
use App\Models\ProjectEditHistory;
use App\Services\FileService;
use App\Traits\ScientificDomainSchemaTrait;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ProjectEditForm extends Component implements HasForms
{
    use InteractsWithForms, ScientificDomainSchemaTrait;

    public $draft;
    public Project $project;
    public array $data = [];
    public array $countries = [];
    public array $continents = [];
    public array $originalDocuments = [];

    public $id;

    protected FileService $fileService;

    public function render()
    {
        return view('livewire.project-edit-form');
    }

    public function __construct()
    {
        $this->fileService = new FileService();
    }

    public function archiveProject()
    {
        $this->project->update(['status' => -1]);

        Notification::make()->title('Le projet a été supprimé avec succès.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();

        return redirect()->route('projects.index');
    }


    public function mount(Project $project, FileService $fileService)
    {
        $this->fileService = $fileService;

        $this->project = $project->load('scientific_domains', 'expenses', 'activities', 'countries', 'continents', 'documents');

        $this->project->contact_ulb = $this->transformContacts($this->project->contact_ulb);
        $this->project->contact_ext = $this->transformContacts($this->project->contact_ext);

        $this->countries = Country::all()->pluck('name', 'id')->toArray();
        $this->continents = Continent::all()->pluck('name', 'code')->toArray();

        $geo_zones = [];

        if ($this->project->countries->isNotEmpty()) {
            foreach ($this->project->countries as $country) {
                $geo_zones[] = 'pays_' . $country->id; // Utiliser l'ID du pays
            }
        }

        if ($this->project->continents->isNotEmpty()) {
            foreach ($this->project->continents as $continent) {
                $geo_zones[] = 'continent_' . $continent->code; // Utiliser le code du continent
            }
        }

        $documents = $this->project->documents->pluck('path')->toArray();
        $this->originalDocuments = $documents;

        $data = array_merge(
            $this->project->toArray(),
            [
                'scientific_domains' => $this->project->scientific_domains->pluck('id')->toArray(),
                'expenses' => $this->project->expenses->pluck('id')->toArray(),
                'activities' => $this->project->activities->pluck('id')->toArray(),
                'geo_zones' => $geo_zones,
                'documents' => $documents,
                'organisation_id' => $this->project->organisation_id,
            ]
        );

        $this->id = $data["id"];
        $this->form->fill($data);
    }

    private function transformContacts($contacts)
    {
        if (is_string($contacts)) {
            $contacts = json_decode($contacts, true);
        }

        if (!is_array($contacts)) {
            return [];
        }

        $transformedContacts = [];

        foreach ($contacts as $contact) {
            $nameParts = explode(' ', $contact['name'], 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            $transformedContacts[] = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $contact['email'] ?? '',
            ];
        }

        return $transformedContacts;
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
                    /*Select::make('organisation_id')
                        ->label('Organisation')
                        ->searchable()
                        ->relationship('organisation', 'title')
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('title')
                                ->label("Nom de l'organisation")
                                ->required(),
                        ])
                        ->required(),*/
                    Checkbox::make('is_big')
                        ->label('Projet majeur')
                        ->default(false),
                    CheckboxList::make('activities')
                        ->label("Catégorie d'activités")
                        ->options(Activity::all()->sortBy('title')->pluck('title', 'id')->toArray())
                        ->columns(2)
                        ->columnSpanFull()
                        ->required(),
                    CheckboxList::make('expenses')
                        ->label("Catégorie de dépenses éligibles")
                        ->options(Expense::all()->sortBy('title')->pluck('title', 'id')->toArray())
                        ->columns(2)
                        ->columnSpanFull()
                        ->required(),
                    \LaraZeus\Accordion\Forms\Accordions::make('Disciplines scientifiques')
                        ->activeAccordion(2)
                        ->isolated()
                        ->accordions([
                            \LaraZeus\Accordion\Forms\Accordion::make('main-data')
                                ->columns()
                                ->label('Disciplines scientifiques')
                                ->schema($this->getFieldsetSchema()),
                        ]),
                    Select::make('geo_zones')
                        ->label("Zones géographiques")
                        ->multiple()
                        ->maxItems(3)
                        ->options(function () {
                            // Initialisation des options avec l'option "Monde entier"
                            $options = [
                                'Monde entier' => 'Monde entier',
                            ];

                            // Récupérer tous les continents en utilisant 'code' comme clé
                            $continents = Continent::all()->pluck('name', 'code')->toArray();

                            // Récupérer tous les pays en utilisant 'id' comme clé
                            $pays = Country::all()->pluck('name', 'id')->toArray();

                            // Ajouter les continents au tableau des options
                            foreach ($continents as $code => $name) {
                                $options["continent_$code"] = $name;
                            }

                            // Ajouter les pays au tableau des options
                            foreach ($pays as $id => $name) {
                                $options["pays_$id"] = $name;
                            }

                            // Retourner les options
                            return $options;
                        })

                ]),

                Tabs\Tab::make('Dates importantes')->schema([
                    Fieldset::make('Deadlines')->schema([
                        Repeater::make('deadlines')->schema([
                            DatePicker::make('date')->label('Date'),
                            TextInput::make('proof')->label('Justificatif'),
                            Checkbox::make('continuous')->default(false)->label('Continu'),
                        ])->label(false)->addActionLabel('+ Ajouter une deadline')->minItems(1)->required()->defaultItems(1),
                    ]),
                ]),
                Tabs\Tab::make('Description')->schema([
                    RichEditor::make('short_description')
                        ->label('Description courte')
                        ->placeholder('Courte et catchy, elle sera visible depuis la page principale et dans la newsletter')
                        ->required()
                        ->live()
                        ->maxLength(500)
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->hint(function ($component, $state) {
                            $cleanedState = strip_tags($state);
                            return strlen($cleanedState) . '/' . $component->getMaxLength() . ' caractères';
                        })
                        ->helperText('Maximum 500 caractères')
                        ->dehydrated(false),
                    TiptapEditor::make('long_description')
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->maxContentWidth('full')
                        ->disableFloatingMenus()
                        ->label('Description complète')
                        ->required(),
                ]),
                Tabs\Tab::make('Budget et dépenses')->schema([
                    TiptapEditor::make('funding')
                        ->label(false)
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->maxContentWidth('full')
                        ->disableFloatingMenus()
                        ->required(),

                ]),
                Tabs\Tab::make("Critères d'admission")->schema([
                    TiptapEditor::make('admission_requirements')
                        ->label(false)
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->maxContentWidth('full')
                        ->disableFloatingMenus()
                        ->required(),
                ]),
                Tabs\Tab::make("Pour postuler")->schema([
                    TiptapEditor::make('apply_instructions')
                        ->label(false)
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->maxContentWidth('full')
                        ->disableFloatingMenus()
                        ->required(),
                ]),
                Tabs\Tab::make("Contacts")->schema([
                    Fieldset::make('Internes')->schema([
                        Repeater::make('contact_ulb')->schema([
                            TextInput::make('first_name')->label('Prénom')->required()->minLength(3),
                            TextInput::make('last_name')->label('Nom')->required()->minLength(3),
                            TextInput::make('email')->label('E-mail')->email()->required()->minLength(5),
                        ])->columns(2)->addActionLabel('+ Nouveau contact')->label(false)->maxItems(3)
                    ]),
                    Fieldset::make('Externes')->schema([
                        Repeater::make('contact_ext')->schema([
                            TextInput::make('first_name')->label('Prénom')->required()->minLength(3),
                            TextInput::make('last_name')->label('Nom')->required()->minLength(3),
                            TextInput::make('email')->label('E-mail')->email()->required()->minLength(5),
                        ])->columns(2)->addActionLabel('+ Nouveau contact')->label(false)->maxItems(3)
                    ]),
                ]),
                Tabs\Tab::make('Documents')->schema([
                    FileUpload::make('documents')
                        ->label('Documents')
                        ->disk('public')
                        ->visibility('public')
                        ->maxSize(20000)
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.oasis.opendocument.text'
                        ])
                        ->multiple()
                        ->moveFiles()
                        ->default(fn() => $this->project->documents->pluck('path')->toArray()),
                ]),
                Tabs\Tab::make('Séances d\'information')->schema([
                    Select::make('info_sessions')
                        ->label('Séances d\'info')
                        ->relationship('info_sessions', 'title')
                        ->multiple()
                        ->searchable()
                        ->options(InfoSession::where('session_datetime', '>', now())
                            ->get()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->id . ' - ' . $item->title];
                            })
                            ->toArray())
                        ->createOptionForm([
                            TextInput::make('title')
                                ->label('Titre')
                                ->required(),
                            RichEditor::make('description')
                                ->toolbarButtons(['underline', 'italic', 'bold'])
                                ->label('Description')
                                ->required()
                                ->extraAttributes(['style' => 'max-height: 200px']),
                            DateTimePicker::make('session_datetime')
                                ->label('Date et heure')
                                ->required(),
                            TextInput::make('speaker')
                                ->label('Présentateur·ice'),
                            Select::make('session_type')
                                ->label('Type de session')
                                ->options([
                                    'Hybride' => 'Hybride',
                                    'Présentiel uniquement' => 'Présentiel uniquement',
                                    'Distanciel uniquement' => 'Distanciel uniquement',
                                ])
                                ->reactive(),
                            TextInput::make('url')
                                ->label('URL de la réunion')
                                ->visible(fn($get) => in_array($get('session_type'), ['Hybride', 'Distanciel uniquement'])),
                            TextInput::make('location')
                                ->label('Adresse')
                                ->visible(fn($get) => in_array($get('session_type'), ['Hybride', 'Présentiel uniquement'])),
                            Select::make('organisation_id')
                                ->relationship('organisation', 'title')
                                ->label('Organisation')
                                ->searchable()
                                ->preload()
                        ])
                ]),
            ]),

            Actions::make([
                Action::make('submit')
                    ->label('Valider les modifications')
                    ->color('primary')
                    ->icon('heroicon-o-check')
                    ->action('submit'),
                Action::make('saveAsDraft')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->label('Garder en brouillon')
                    ->color('info')
                    ->action('saveAsDraft'),
                Action::make('copy')
                    ->icon('heroicon-o-document-duplicate')
                    ->label('Copier')
                    ->color('info')
                    ->action('copyProject'),
                Action::make('archive')
                    ->label('Supprimer')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer le projet.')
                    ->modalDescription('Voulez-vous vraiment supprimer ce projet ?.')
                    ->action('archiveProject')
                    ->button(),
            ])->alignEnd()
        ])->statePath('data')->model($this->project); //sauvegarde todo
    }

    public function submit(): void
    {
        if (!$this->fileService) {
            $this->fileService = app(FileService::class);
        }
        $userId = Auth::id();

        $rules = [
            'title' => 'required|string|max:255',
            'is_big' => 'boolean',
            'organisation_id' => 'required|exists:organisations,id',
            "expenses" => 'array',
            'activities' => 'array',
            'documents' => 'array',
            'scientific_domains' => 'required|array|min:1',
            'scientific_domains.*' => 'integer|exists:scientific_domains,id',
            'geo_zones' => 'array',
            'deadlines' => 'array',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'array',
            'funding' => 'array|nullable',
            'admission_requirements' => 'array|nullable',
            'apply_instructions' => 'array|nullable',
            'contact_ulb' => 'array',
            'contact_ulb.*.first_name' => 'nullable|string',
            'contact_ulb.*.last_name' => 'nullable|string',
            'contact_ulb.*.email' => 'nullable|email',
            'contact_ext' => 'array',
            'contact_ext.*.first_name' => 'string|max:50',
            'contact_ext.*.last_name' => 'string|max:50',
            'contact_ext.*.email' => 'email|max:255',
            'status' => 'integer',
            'is_draft' => 'boolean',
            'info_sessions' => 'nullable|array'
        ];

        $messages = [
            'title.required' => 'Le titre est requis.',
            'title.string' => 'Le titre doit être une chaîne de caractères.',
            'title.max' => 'Le titre ne peut pas dépasser :max caractères.',
            'is_big.boolean' => 'Le champ "Projet Majeur" doit être vrai ou faux.',
            'organisation_id.required' => 'Le champ Organisation est requis.',
            'organisation_id.exists' => 'L\'organisation sélectionnée n\'existe pas.',
            'activities.array' => 'Les catégories d\'activité doivent être remplis.',
            'expenses.array' => 'Les catégories de dépenses éligibles doivent être remplis.',
            'documents.array' => 'Les documents doivent être remplis.',
            'scientific_domains.array' => 'Les disciplines scientifiques doivent être remplies.',
            'scientific_domains.required' => 'Veuillez sélectionner au moins une discipline scientifique.',
            'scientific_domains.min' => 'Veuillez sélectionner au moins une discipline scientifique.',
            'scientific_domains.*.integer' => 'Chaque discipline scientifique sélectionnée doit être valide.',
            'scientific_domains.*.exists' => 'La discipline scientifique sélectionnée est invalide.',
            'geo_zones.array' => 'Les zones géographiques doivent être remplies.',
            'deadlines.array' => 'Les deadlines doivent être remplies.',
            'short_description.string' => 'La description courte doit être une chaîne de caractères.',
            'short_description.max' => 'La description courte ne peut pas dépasser :max caractères.',
            'long_description.array' => 'La description longue doit être remplie.',
            'funding.array' => 'Le champs "Budget & dépenses" doit être rempli.',
            'apply_instructions.array' => 'Les instructions pour postuler doivent être remplis.',
            'contact_ulb.*.first_name.string' => 'Le prénom du contact interne doit être une chaîne de caractères.',
            'contact_ulb.*.last_name.string' => 'Le nom du contact interne doit être une chaîne de caractères.',
            'contact_ulb.*.email.email' => 'L\'email du contact interne doit être une adresse email valide.',
            'contact_ext.*.first_name.string' => 'Le prénom du contact externe doit être une chaîne de caractères.',
            'contact_ext.*.first_name.max' => 'Le prénom du contact externe ne peut pas dépasser :max caractères.',
            'contact_ext.*.last_name.string' => 'Le nom du contact externe doit être une chaîne de caractères.',
            'contact_ext.*.last_name.max' => 'Le nom du contact externe ne peut pas dépasser :max caractères.',
            'contact_ext.*.email.email' => 'L\'email du contact externe doit être une adresse email valide.',
            'contact_ext.*.email.max' => 'L\'email du contact externe ne peut pas dépasser :max caractères.',
            'at_least_one_contact' => 'Veuillez fournir au moins un contact interne ou externe.',
            'status.integer' => 'Le statut doit être un nombre entier.',
            'is_draft.boolean' => 'Le champ "Brouillon" doit être vrai ou faux.',
            'info_sessions.array' => 'Les séances d\'informations doivent être remplies.'
        ];

        $validator = Validator::make($this->data, $rules, $messages, [
            'title' => 'Titre',
            'is_big' => 'Projet Majeur',
            'organisation_id' => 'Organisation',
            'activities' => 'Catégorie d\'activités',
            'expenses' => 'Catégorie de dépenses éligibles',
            'scientific_domains' => 'Disciplines scientifiques',
            'geo_zones' => 'Zones géographiques',
            'deadlines' => 'Deadlines',
            // 'date_lessor' => 'Date Bailleur',
            'short_description' => 'Description courte',
            'long_description' => 'Description longue',
            'funding' => 'Budget et dépenses',
            'admission_requirements' => 'Critères d\'admission',
            'apply_instructions' => 'Pour postuler',
            'contact_ulb.*.first_name' => 'Prénom',
            'contact_ulb.*.last_name' => 'Nom',
            'contact_ulb.*.email' => 'Email',
            'contact_ext.*.first_name' => 'Prénom',
            'contact_ext.*.last_name' => 'Nom',
            'contact_ext.*.email' => 'Email',
            'status' => 'Status',
            'is_draft' => 'Brouillon',
            'info_sessions' => 'Séance d\'informations'
        ]);
        $validator->after(function ($validator) {
            $contact_ulb = $this->data['contact_ulb'] ?? [];
            $contact_ext = $this->data['contact_ext'] ?? [];

            // Filtrer les contacts vides
            $contact_ulb = array_filter($contact_ulb, function ($contact) {
                return !empty(trim($contact['first_name'] ?? ''))
                    && !empty(trim($contact['last_name'] ?? ''))
                    && !empty(trim($contact['email'] ?? ''));
            });

            $contact_ext = array_filter($contact_ext, function ($contact) {
                return !empty(trim($contact['first_name'] ?? ''))
                    && !empty(trim($contact['last_name'] ?? ''))
                    && !empty(trim($contact['email'] ?? ''));
            });

            // Vérification si au moins un contact ULB ou externe est fourni
            $ulb_has_contact = !empty($contact_ulb);
            $ext_has_contact = !empty($contact_ext);

            if (!$ulb_has_contact && !$ext_has_contact) {
                $validator->errors()->add('contact_ulb', 'Veuillez fournir au moins un contact interne ou externe avec les informations complètes.');
            }
        });

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::make()
                    ->title($error)
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->seconds(5)
                    ->send();
            }
        } else {
            $data = $validator->validated();
            try {
                $data['last_update_user_id'] = $userId;
                /* REMOVED causait des problèmes d'interprétation de l'éditeur lors de la modification BLABLOU
                $converter = new HtmlConverter();
                $markdown = $converter->convert($this->data["short_description"]);

                $data['short_description'] = $markdown;
                */

                if (isset($data['contact_ulb'])) {
                    $contactsUlB = [];
                    foreach ($data['contact_ulb'] as $contact) {
                        $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                        $email = $contact['email'] ?? '';

                        if ($name !== '' || $email !== '') {
                            $contactsUlB[] = [
                                'name' => $name,
                                'email' => $email,
                            ];
                        }
                    }
                    $data['contact_ulb'] = $contactsUlB;
                } else {
                    $data['contact_ulb'] = [];
                }

                if (isset($data['contact_ext'])) {
                    $contactsExt = [];
                    foreach ($data['contact_ext'] as $contact) {
                        $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                        $email = $contact['email'] ?? '';
                        $phone = $contact['tel'] ?? '';

                        if ($name !== '' || $email !== '') {
                            $contactsExt[] = [
                                'name' => $name,
                                'email' => $email,
                            ];
                        }
                    }
                    $data['contact_ext'] = $contactsExt;
                } else {
                    $data['contact_ext'] = [];
                }

                $this->project->update($data);

                $this->project->expenses()->sync($data['expenses'] ?? []);
                $this->project->activities()->sync($data['activities'] ?? []);
                $this->project->scientific_domains()->sync($data['scientific_domains'] ?? []);
                $this->project->info_sessions()->sync($data['info_sessions'] ?? []);

                if (!empty($data['documents'])) {
                    $this->fileService->handleDocumentUpdates($data['documents'], $this->project);
                }

                if (!empty($data['geo_zones'])) {
                    $continentIds = [];
                    $countryIds = [];

                    foreach ($data['geo_zones'] as $zone) {
                        if (strpos($zone, 'continent_') === 0) {
                            $continent_code = str_replace('continent_', '', $zone); // Extraire le code du continent
                            $continentIds[] = $continent_code; // Ajouter à la liste des continents
                        } elseif (strpos($zone, 'pays_') === 0) {
                            $country_id = str_replace('pays_', '', $zone); // Extraire l'ID du pays
                            $countryIds[] = $country_id; // Ajouter à la liste des pays
                        }
                    }

                    // Synchroniser les continents associés au projet (Many-to-Many)
                    if (!empty($continentIds)) {
                        $this->project->continents()->sync($continentIds); // Synchroniser les continents du projet
                    }

                    // Synchroniser les pays associés au projet (Many-to-Many)
                    if (!empty($countryIds)) {
                        $this->project->countries()->sync($countryIds); // Synchroniser les pays du projet
                    }
                }


                $id = $this->project->id;

                ProjectEditHistory::create([
                    'date' => Date::now(),
                    'project_id' => $id,
                    'user_id' => $userId
                ]);
                $this->project->save();
                Notification::make()->title('Le projet a été modifié avec succès.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();
                redirect()->route('projects.index');
            } catch (\Exception $e) {
                Notification::make()->title("Le projet n'a pas pu être modifié.")->icon('heroicon-o-x-circle')->seconds(5)->color('danger')->send();

                redirect()->route('projects.index');
            }
        }
    }

    public function replicateModelWithRelations($model)
    {
        $model->load('scientific_domains', 'expenses', 'activities', 'countries', 'continents', 'documents');

        $newModel = $model->replicate();
        $newModel->save();

        $newModel->scientific_domains()->sync($model->scientific_domains->pluck('id')->toArray());
        $newModel->expenses()->sync($model->expenses->pluck('id')->toArray());
        $newModel->activities()->sync($model->activities->pluck('id')->toArray());
        $newModel->countries()->sync($model->countries->pluck('id')->toArray());
        $newModel->continents()->sync($model->continents->pluck('code')->toArray());

        foreach ($model->documents as $document) {
            $newDocument = $document->replicate();
            $newDocument->project_id = $newModel->id;
            $newDocument->save();
        }
        $newModel->save();

        return $newModel;
    }

    public function copyProject()
    {
        $project = $this->replicateModelWithRelations($this->project);

        Notification::make()->title('Le projet a été copié avec succès.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();

        return redirect()->route('projects.show', $project->id);
    }

    public function saveAsDraft()
    {
        $userId = Auth::id();

        if (!$this->fileService) {
            $this->fileService = app(FileService::class);
        }

        if (!empty($this->data['documents'])) {
            $lastdoc = $this->draft ? $this->draft->content['documents'] : [];
            $docs = $this->fileService->moveForDraft($this->data['documents'], $lastdoc);
            $this->data["documents"] = $docs;
        }

        if ($this->draft) {
            $updatedDraft = Draft::find($this->draft->id);

            if ($updatedDraft) {
                $updateSuccessful = $updatedDraft->update([
                    'content' => $this->data,
                ]);

                $updatedDraft->users()->syncWithoutDetaching($userId);

                if ($updateSuccessful) {
                    redirect()->route('profile.show')->with('success', 'Le brouillon a bien été enregistré.');
                }
            }
        }

        $draft = new Draft([
            'content' => $this->data,
        ]);

        if ($draft->save()) {
            $draft->users()->attach($userId);

            Notification::make()
                ->title('Brouillon enregistré.')
                ->send()
                ->seconds(5)
                ->color('success');
            redirect()->route('profile.show');
        } else {
            // Gérer le cas où la sauvegarde du nouveau brouillon échoue
            Notification::make()
                ->title('La sauvegarde du brouillon a échoué.')
                ->send()
                ->seconds(5)
                ->color('danger');
            redirect()->back();
        }
    }

    private function handleDocumentUpdates(array $newDocuments, Project $project)
    {
        $existingDocuments = $project->documents->pluck('path')->toArray();

        $deletedDocuments = array_diff($existingDocuments, $newDocuments);

        foreach ($deletedDocuments as $deletedDocument) {
            Storage::disk('public')->delete($deletedDocument);
            Document::where('filename', $deletedDocument)->where('project_id', $project->id)->delete();
        }

        $this->moveFiles($newDocuments, $project);
    }

    private function moveFiles(array $files, Project $project): array
    {
        $movedFiles = [];
        foreach ($files as $file) {
            if (is_string($file)) {
                continue;
            }

            $finalPath = 'uploads/docs/' . $file->getFilename();

            Storage::disk('public')->putFileAs(
                'uploads/docs',
                $file,
                $file->getFilename()
            );

            $document = Document::create([
                'project_id' => $project->id,
                'filename' => $file->getClientOriginalName(),
                'path' => $finalPath,
                'download_count' => 0,
            ]);

            $movedFiles[] = $document->id;

            // Optionally delete the temp file
            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }
        }

        return $movedFiles;
    }
}
