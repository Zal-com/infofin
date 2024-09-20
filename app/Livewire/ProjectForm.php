<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Countries;
use App\Models\Document;
use App\Models\Draft;
use App\Models\InfoSession;
use App\Models\InfoType;
use App\Models\Project;
use App\Models\ScientificDomain;
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
                    $this->project->{$attribute} = $data[$attribute][0];
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
                                ->label('Organisation Title')
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
                    /*
                    Select::make('scientific_domains')
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
                    */
                    Select::make('geo_zones')
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
                    Fieldset::make('Deadlines')->schema([
                        Repeater::make('deadlines')->schema([
                            DatePicker::make('date')->label('Date'),
                            TextInput::make('proof')->label('Justificatif'),
                            Checkbox::make('continuous')->default(false)->label('Continu'),
                        ])->label(false)->addActionLabel('+ Ajouter une deadline')->minItems(1)->required()->defaultItems(1),
                    ]),
                    Select::make('periodicity')
                        ->label('Periodicité')
                        ->options(['Sans', 'Annuel', 'Biennal', 'Triennal', 'Quadriennal', 'Quinquennal'])
                        ->selectablePlaceholder(false)
                        ->default(0),
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
                        ->required()
                        ->placeholder('Informations sur le montant du financement, sa durée, etc.'),
                ]),
                Tabs\Tab::make("Critères d'admission")->schema([
                    TiptapEditor::make('admission_requirements')
                        ->label(false)
                        ->output(TiptapOutput::Json)
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->maxContentWidth('full')
                        ->disableFloatingMenus()
                        ->required(),
                ]),
                Tabs\Tab::make("Pour postuler")->schema([
                    TiptapEditor::make('apply_instructions')
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->maxContentWidth('full')
                        ->disableFloatingMenus()
                        ->label(false)
                        ->required(),

                ]),
                Tabs\Tab::make("Contacts")->schema([
                    Fieldset::make('Internes')->schema([
                        Repeater::make('contact_ulb')->schema([
                            TextInput::make('first_name')->label('Prénom'),
                            TextInput::make('last_name')->label('Nom'),
                            TextInput::make('email')->label('E-mail')->email(),
                            TextInput::make('tel')->label('Numéro de téléphone')->tel(),
                            TextInput::make('address')->label('Adresse')->columnSpan(2)
                        ])->columns(2)->addActionLabel('+ Nouveau contact')->label(false)->maxItems(3)
                    ]),
                    Fieldset::make('Externes')->schema([
                        Repeater::make('contact_ext')->schema([
                            TextInput::make('first_name')->label('Prénom'),
                            TextInput::make('last_name')->label('Nom'),
                            TextInput::make('email')->label('E-mail')->email(),
                            TextInput::make('tel')->label('Numéro de téléphone')->tel(),
                            TextInput::make('address')->label('Adresse')->columnSpan(2)
                        ])->columns(2)->addActionLabel('+ Nouveau contact')->label(false)->maxItems(3)
                    ]),
                ]),
                Tabs\Tab::make('Documents')->schema([
                    FileUpload::make('documents')
                        ->label('Documents')
                        ->disk('public')
                        ->visibility('public')
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
                    ->schema([
                        Select::make('info_sessions')
                            ->label('Séances d\'info')
                            ->relationship('info_sessions', 'title')
                            ->multiple()
                            ->searchable()
                            ->options(InfoSession::all()->pluck('title', 'id')->toArray())
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
                            ])
                    ])
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
        $this->data['documents'] = $this->fileService->previewFile($this->data['documents']);
        session()->flash('previewData', $this->data);
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
            'scientific_domains' => 'array',
            'geo_zones' => 'array',
            'deadlines' => 'array',
            'periodicity' => 'nullable|integer',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'array',
            'funding' => 'array',
            'admission_requirements' => 'array',
            'apply_instructions' => 'array',
            'contact_ulb.*.first_name' => 'nullable|string',
            'contact_ulb.*.last_name' => 'nullable|string',
            'contact_ulb.*.email' => 'nullable|email',
            'contact_ulb.*.tel' => 'nullable|phone:BE',
            'contact_ulb.*.address' => 'nullable|string',
            'contact_ext.*.first_name' => 'nullable|string|max:50',
            'contact_ext.*.last_name' => 'nullable|string|max:50',
            'contact_ext.*.email' => 'nullable|email|max:255',
            'contact_ext.*.tel' => 'nullable|phone:BE',
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
            'geo_zones.array' => 'Les zones géographiques doivent être remplies.',
            'deadlines.array' => 'Les deadlines doivent être remplies.',
            'periodicity.integer' => 'La périodicité doit être un nombre entier.',
            'short_description.string' => 'La description courte doit être une chaîne de caractères.',
            'short_description.max' => 'La description courte ne peut pas dépasser :max caractères.',
            'long_description.array' => 'La description longue doit être remplie.',
            'funding.array' => 'Le champs "Budget & dépenses" doit être rempli.',
            'admission_requirements.array' => 'Les critères d\'admission doivent être remplis.',
            'apply_instructions.array' => 'Les instructions pour postuler doivent être remplis.',
            'contact_ulb.*.first_name.string' => 'Le prénom du contact interne doit être une chaîne de caractères.',
            'contact_ulb.*.last_name.string' => 'Le nom du contact interne doit être une chaîne de caractères.',
            'contact_ulb.*.email.email' => 'L\'email du contact interne doit être une adresse email valide.',
            'contact_ulb.*.tel.phone' => 'Le téléphone du contact interne doit être un numéro valide en Belgique.',
            'contact_ulb.*.address.string' => 'L\'adresse du contact interne doit être une chaîne de caractères.',
            'contact_ext.*.first_name.string' => 'Le prénom du contact externe doit être une chaîne de caractères.',
            'contact_ext.*.first_name.max' => 'Le prénom du contact externe ne peut pas dépasser :max caractères.',
            'contact_ext.*.last_name.string' => 'Le nom du contact externe doit être une chaîne de caractères.',
            'contact_ext.*.last_name.max' => 'Le nom du contact externe ne peut pas dépasser :max caractères.',
            'contact_ext.*.email.email' => 'L\'email du contact externe doit être une adresse email valide.',
            'contact_ext.*.email.max' => 'L\'email du contact externe ne peut pas dépasser :max caractères.',
            'contact_ext.*.tel.phone' => 'Le téléphone du contact externe doit être un numéro valide en Belgique.',
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
            'periodicity' => 'Périodicité',
            // 'date_lessor' => 'Date Bailleur',
            'short_description' => 'Description courte',
            'long_description' => 'Description longue',
            'funding' => 'Budget et dépenses',
            'admission_requirements' => 'Critères d\'admission',
            'apply_instructions' => 'Pour postuler',
            'contact_ulb.*.first_name' => 'Prénom',
            'contact_ulb.*.last_name' => 'Nom',
            'contact_ulb.*.email' => 'Email',
            'contact_ulb.*.tel' => 'Téléphone',
            'contact_ulb.*.address' => 'Addresse',
            'contact_ext.*.first_name' => 'Prénom',
            'contact_ext.*.last_name' => 'Nom',
            'contact_ext.*.email' => 'Email',
            'contact_ext.*.tel' => 'Téléphone',
            'status' => 'Status',
            'is_draft' => 'Brouillon',
            'info_sessions' => 'Séance d\'informations'
        ]);

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

            if ($data['periodicity'] === null) {
                $data['periodicity'] = 0;
            }

            $contactsUlB = [];
            if (isset($data['contact_ulb'])) {
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
                $data['contact_ulb'] = !empty($contactsUlB) ? $contactsUlB : [];
            } else {
                $data['contact_ulb'] = [];
            }


            $contactsExt = [];
            if (isset($data["contact_ext"])) {
                foreach ($data['contact_ext'] as $contact) {
                    $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                    $email = $contact['email'] ?? '';
                    $phone = $contact['tel'] ?? '';
                    $address = $contact['address'] ?? '';

                    if ($name !== '' || $email !== '' || $phone !== '' || $address !== '') {
                        $contactsExt[] = [
                            'name' => $name,
                            'email' => $email,
                            'phone' => $phone,
                            'address' => $address,
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
                    foreach ($data['geo_zones'] as $zone) {
                        if (strpos($zone, 'continent_') === 0) {
                            $continent_id = str_replace('continent_', '', $zone);
                            $project->continent()->associate($continent_id);
                        } elseif (strpos($zone, 'pays_') === 0) {
                            $country_id = str_replace('pays_', '', $zone);
                            $project->country()->associate($country_id);
                        }
                    }

                    $project->save();
                }
                Notification::make()->title('Votre appel a bien été ajouté.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();
                return redirect()->route('projects.index');
            }

        }
    }
}
