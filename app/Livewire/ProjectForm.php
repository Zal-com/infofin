<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Country;
use App\Models\Document;
use App\Models\Draft;
use App\Models\InfoSession;
use App\Models\InfoType;
use App\Models\Project;
use App\Models\ScientificDomainCategory;
use App\Services\FileService;
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
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use League\HTMLToMarkdown\HtmlConverter;
use Livewire\Component;

final class ProjectForm extends Component implements HasForms
{
    use InteractsWithForms;

    public $draft;
    public Project $project;
    public array $data = [];
    public $fromPrev;
    protected $fileService;
    public $infoSessionsOptions = [];

    public function mount(FileService $fileService, Project $project = null)
    {
        $this->fileService = $fileService;
        if (session()->has('fromPreviewData')) {
            $this->initializeProjectFromData(session('fromPreviewData'));
        } elseif ($this->draft) {
            $this->initializeProjectFromData($this->draft->content);
        } else {
            $this->project = $project ?? new Project();
        }

        $this->form->fill($this->project->toArray());
        $this->refreshInfoSessionsOptions();
    }

    public function refreshInfoSessionsOptions()
    {
        $this->infoSessionsOptions = InfoSession::where('session_datetime', '>', now())
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->id . ' - ' . $item->title];
            })
            ->toArray();
    }

    private function initializeProjectFromData(array $data)
    {
        $this->fromPrev = $data;
        $this->project = new Project($data);

        if (!empty($data['documents'])) {
            $data['document_filenames'] = array_map(function ($docPath) {
                if (is_array($docPath)) {
                    $document = Document::where('path', $docPath['path'])->first();
                    return $document ? $document->filename : basename($docPath['path']);
                } else {
                    $document = Document::where('path', $docPath)->first();
                    return $document ? $document->filename : basename($docPath);
                }
            }, $data['documents']);
        }

        foreach (['organisation', 'scientific_domains', 'info_types', 'geo_zones', 'documents', 'document_filenames', 'info_sessions'] as $attribute) {
            if (isset($data[$attribute])) {
                if ($attribute == 'organisation') {
                    $this->project->{$attribute} = $data[$attribute];
                } else {
                    $this->project->{$attribute} = $data[$attribute];
                }
            }
        }
    }

    protected function getFieldsetSchema(): array
    {
        $categories = ScientificDomainCategory::with('domains')->get();
        $fieldsets = [];

        foreach ($categories as $category) {
            $sortedDomains = $category->domains->sortBy('name')->pluck('name', 'id')->toArray();
            $fieldsets[] = Fieldset::make($category->name)
                ->schema([
                    CheckboxList::make('scientific_domains')
                        ->label(false)
                        ->options($sortedDomains)
                        ->bulkToggleable()
                        ->columnSpan(2)
                        ->required()
                        ->extraAttributes([
                            'class' => 'w-full'
                        ])->columns(3)
                ])
                ->columnSpan(3)
                ->extraAttributes([
                    'class' => 'w-full disciplines-fieldset',
                ]);
        }

        return $fieldsets;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Tabs')->tabs([
                Tabs\Tab::make('Informations')->schema([
                    TextInput::make('title')
                        ->label('Titre')
                        ->placeholder('Titre concis, évitez les acronymes seuls')
                        ->maxLength(255)
                        ->required()
                        ->autofocus(),
                    Select::make('organisation_id')
                        ->label('Organisation')
                        ->searchable()
                        ->relationship('organisation', 'title')
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('title')
                                ->label("Nom de l'organisation")
                                ->required(),
                        ])
                        ->required(),
                    Checkbox::make('is_big')
                        ->label('Projet majeur')
                        ->default(false),
                    TextInput::make('origin_url')
                        ->label('URL vers l\'appel original')
                        ->url(),
                    CheckboxList::make('info_types')
                        ->label('Types de programmes')
                        ->options(InfoType::all()->sortBy('title')->pluck('title', 'id')->toArray())
                        ->columns(3)
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
                    /*
                    DatePicker::make('date_lessor')
                        ->label('Date Bailleur'),
                    */
                ]),
                Tabs\Tab::make('Description')->schema([
                    RichEditor::make('short_description')
                        ->label('Description courte')
                        ->placeholder('Courte et catchy, elle sera visible depuis la page principale et dans la newsletter')
                        ->required()
                        ->live()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->extraAttributes(['maxlength' => 500, 'script']) // This ensures the frontend enforces the limit
                        ->maxLength(500) // This ensures the backend enforces the limit
                        ->extraAttributes(['maxlength' => 500, 'script' => ""]) // This ensures the frontend enforces the limit
                        ->hint(function ($component, $state) {
                            $cleanedState = strip_tags($state);
                            return strlen($cleanedState) . '/' . $component->getMaxLength() . ' caractères';
                        })
                        ->afterStateHydrated(function ($component, $state) {
                            if (strlen($state) >= 500) {
                                $component->disabled(true);
                            }
                        })
                        ->dehydrated(false)
                        ->reactive(),
                    TiptapEditor::make('long_description')
                        ->profile('default')
                        ->output(TiptapOutput::Json)
                        ->columnSpan(1)
                        ->maxContentWidth('full')
                        ->label('Description complète')
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->disableFloatingMenus()
                        ->placeholder('Description la plus complète possible du projet, aucune limite de caractères')
                        ->required(),
                ]),
                Tabs\Tab::make('Budget et dépenses')->schema([
                    TiptapEditor::make('funding')
                        ->label(false)
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->output(TiptapOutput::Json)
                        ->maxContentWidth('full')
                        ->disableFloatingMenus()
                        ->placeholder('Informations sur le montant du financement, sa durée, etc.'),
                ]),
                Tabs\Tab::make("Critères d'admission")->schema([
                    TiptapEditor::make('admission_requirements')
                        ->label(false)
                        ->output(TiptapOutput::Json)
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->maxContentWidth('full')
                        ->disableFloatingMenus(),
                ]),
                Tabs\Tab::make("Pour postuler")->schema([
                    TiptapEditor::make('apply_instructions')
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->maxContentWidth('full')
                        ->disableFloatingMenus()
                        ->label(false),

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
                        ->maxSize(2000)
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.oasis.opendocument.text'
                        ])
                        ->multiple()
                        ->moveFiles(),
                ]),
                Tabs\Tab::make('info_sessions')
                    ->label("Séances d'information")
                    ->live()
                    ->schema([
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
                                    ->required()
                                    ->string()
                                    ->columnSpanFull()
                                ,
                                RichEditor::make('description')
                                    ->toolbarButtons(['underline', 'italic', 'bold'])
                                    ->label('Description')
                                    ->required()
                                    ->string()
                                    ->extraAttributes(['style' => 'max-height: 200px'])
                                    ->columnSpanFull(),
                                \LaraZeus\Accordion\Forms\Accordions::make('Disciplines scientifiques')
                                    ->activeAccordion(2)
                                    ->isolated()
                                    ->accordions([
                                        \LaraZeus\Accordion\Forms\Accordion::make('info-data')
                                            ->columns()
                                            ->label('Disciplines scientifiques')
                                            ->schema($this->getFieldsetSchema()),
                                    ]),
                                DateTimePicker::make('session_datetime')
                                    ->seconds(false)
                                    ->label('Date et heure')
                                    ->columnSpan(1)
                                    ->required(),
                                TextInput::make('speaker')
                                    ->label('Présentateur·ice')
                                    ->string()
                                    ->columnSpan(1),
                                Select::make('session_type')
                                    ->label('Type de session')
                                    ->options([
                                        2 => 'Hybride',
                                        1 => 'Présentiel',
                                        0 => 'Distanciel',
                                    ])
                                    ->reactive(),
                                TextInput::make('url')
                                    ->required()
                                    ->label('URL de la réunion')
                                    ->visible(fn($get) => in_array($get('session_type'), [0, 2]))
                                    ->reactive(),
                                TextInput::make('location')
                                    ->required()
                                    ->label('Adresse')
                                    ->visible(fn($get) => in_array($get('session_type'), [1, 2])),
                                Select::make('organisation_id')
                                    ->required()
                                    ->relationship('organisation', 'title')
                                    ->label('Organisation')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('title')
                                            ->required(),
                                    ])
                            ])->createOptionUsing(function ($data, $set) {
                                try {
                                    $info_session = InfoSession::create([
                                        'title' => $data['title'],
                                        'description' => $data['description'],
                                        'session_datetime' => $data['session_datetime'],
                                        'speaker' => $data['speaker'],
                                        'session_type' => $data['session_type'],
                                        'location' => $data['location'],
                                        'organisation_id' => $data['organisation_id'],
                                    ]);
                                    $info_session->scientific_domains()->attach($data['scientific_domains']);

                                    Notification::make()
                                        ->icon('heroicon-o-check')
                                        ->color('success')
                                        ->iconColor('success')
                                        ->title('Session d\'info créée avec succès.')
                                        ->seconds(5)
                                        ->send();

                                    $this->refreshInfoSessionsOptions();
                                    $set('info_sessions', $this->infoSessionsOptions);

                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->icon('heroicon-o-x')
                                        ->color('danger')
                                        ->iconColor('danger')
                                        ->title('Quelque chose ne s\'est pas passé comme prévu. Veuillez réessayer.')
                                        ->seconds(5)
                                        ->send();
                                }
                            })
                    ])->afterStateUpdated(function (Set $set, $state) {
                        $this->refreshInfoSessionsOptions();
                        $set('info_sessions', $this->infoSessionsOptions);
                    })
            ]),
            Actions::make([
                Action::make('submit')
                    ->label('Valider')
                    ->color('primary')
                    ->icon('heroicon-o-check')
                    ->action('submit'),
                Action::make('preview')
                    ->label('Prévisualiser')
                    ->icon('heroicon-o-eye')
                    ->color('tertiary')
                    ->action('preview'),
                Action::make('saveAsDraft')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->label('Garder en brouillon')
                    ->color('info')
                    ->action('saveAsDraft')
            ])->alignEnd()
        ])->statePath('data')->model($this->project);
    }

    public function render()
    {
        return view('livewire.project-form');
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
                $updatedDraft->update([
                    'content' => $this->data,
                ]);

                $updatedDraft->users()->syncWithoutDetaching($userId);

                Notification::make()
                    ->title("Brouillon mis à jour")
                    ->color('success')
                    ->seconds(5)
                    ->send();

                return redirect()->route('profile.show');
            }
        }

        $draft = new Draft([
            'content' => $this->data,
        ]);

        if ($draft->save()) {
            $draft->users()->attach($userId);

            Notification::make()
                ->title('Brouillon enregistré.')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->send();

            return redirect()->route('profile.show');
        } else {
            Notification::make()
                ->title("La sauvegarde du brouillon a échoué")
                ->color('danger')
                ->seconds(5)
                ->send();

            return redirect()->back();
        }
    }


    public function preview()
    {
        if (!$this->fileService) {
            $this->fileService = app(FileService::class);
        }
        session()->forget("previewData");
        $this->data['documents'] = $this->fileService->previewFile($this->data['documents']);
        session()->put('previewData', $this->data);
        return redirect()->route('projects.preview');
    }

    public function submit()
    {
        if (!$this->fileService) {
            $this->fileService = app(FileService::class);
        }
        $userId = Auth::id();

        $rules = [
            'title' => 'required|string|max:255',
            'is_big' => 'boolean',
            'organisation_id' => 'required|exists:organisations,id',
            'info_types' => 'array',
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
            'info_types.array' => 'Les types de programme doivent être remplis.',
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
            'info_types' => 'Types de programme',
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
                Notification::make()->title($error)->icon('heroicon-o-x-circle')->seconds(5)->color('danger')->send();
            }
        } else {
            $data = $validator->validated();
            $converter = new HtmlConverter();
            $markdown = $converter->convert($this->data["short_description"]);

            $data['short_description'] = $markdown;

            $data['poster_id'] = $userId;
            $data['last_update_user_id'] = $userId;

            $contactsUlB = [];
            if (isset($data['contact_ulb'])) {
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
                $data['contact_ulb'] = !empty($contactsUlB) ? $contactsUlB : [];
            } else {
                $data['contact_ulb'] = [];
            }


            $contactsExt = [];
            if (isset($data["contact_ext"])) {
                foreach ($data['contact_ext'] as $contact) {
                    $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                    $email = $contact['email'] ?? '';

                    if ($name !== '' || $email !== '') {
                        $contactsExt[] = [
                            'name' => $name,
                            'email' => $email,
                        ];
                    }
                }
                $data['contact_ext'] = !empty($contactsExt) ? $contactsExt : [];
            } else {
                $data['contact_ext'] = [];
            }

            if ($project = Project::create($data)) {
                if (!empty($data['info_types'])) {
                    $project->info_types()->sync($data['info_types']);
                }

                if (!empty($data['scientific_domains'])) {
                    $project->scientific_domains()->sync($data['scientific_domains']);
                }

                if (!empty($data['info_sessions'])) {
                    $project->info_sessions()->sync($data['info_sessions']);
                }

                if (isset($data['documents']) && count($data['documents']) > 0) {
                    $this->fileService->moveFiles($data['documents'], $project);
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
                        $project->continents()->sync($continentIds); // Synchroniser les continents du projet
                    }

                    // Synchroniser les pays associés au projet (Many-to-Many)
                    if (!empty($countryIds)) {
                        $project->countries()->sync($countryIds); // Synchroniser les pays du projet
                    }
                }

                $project->save();
                Notification::make()->title('Votre appel a bien été ajouté.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();
                return redirect()->route('projects.index');
            }

        }
    }
}
