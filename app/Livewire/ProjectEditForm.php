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
use App\Traits\ReplicateModelWithRelations;
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
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ProjectEditForm extends Component implements HasForms
{
    use InteractsWithForms, ScientificDomainSchemaTrait;
    use ReplicateModelWithRelations;

    public $draft;
    public Project $project;
    public array $data = [];
    public array $countries = [];
    public array $continents = [];
    public array $originalDocuments = [];

    public $id;

    public $oldProject;

    protected FileService $fileService;

    public bool $showModal = false; // Contrôle l'affichage du modal
    public bool $isInNextEmail = false; // Décision de l'utilisateur pour la newsletter

    public function render()
    {
        return view('livewire.project-edit-form', [
            'showModal' => $this->showModal,
            'isInNextEmail' => $this->isInNextEmail,
        ]);
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
        $this->oldProject = $data;
        $this->form->fill($data);
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
                        ->autofocus()
                        ->columnSpanFull()
                        ->validationAttribute('Titre')
                        ->validationMessages([
                            'required' => 'Le champ ":attribute" est obligatoire.',
                            'max' => 'Le champ ":attribute" ne peut pas excéder :max caractères de long.',
                        ]),
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
                        ->required()
                        ->columnSpanFull()
                        ->validationAttribute('Organisation')
                        ->validationMessages([
                            'required' => 'Le champ ":attribute" est obligatoire.',
                        ]),
                    Checkbox::make('is_big')
                        ->label('Projet majeur')
                        ->default(false)
                        ->columnSpanFull(),
                    TextInput::make('origin_url')
                        ->label('URL vers l\'appel original')
                        ->url()
                        ->activeUrl()
                        ->nullable()
                        ->columnSpanFull(),
                    Fieldset::make('activities_fieldset')->schema([
                        CheckboxList::make('activities')
                            ->label(new HtmlString("<strong>Catégories d'activités</strong>"))
                            ->options(Activity::all()->sortBy('title')->pluck('title', 'id')->toArray())
                            ->required()
                            ->bulkToggleable()
                            ->minItems(1)
                            ->validationAttribute('Catégories d\'activité')
                            ->validationMessages([
                                'required' => 'Le champ ":attribute" est obligatoire.',
                                'min' => 'Le champ ":attribute" doit comprendre au moins :min élément.',
                            ]),
                    ])
                        ->label(false)
                        ->columnSpan(1),
                    Fieldset::make('expenses_fieldset')->schema([
                        CheckboxList::make('expenses')
                            ->label(new HtmlString("<strong>Catégorie de dépenses éligibles</strong>"))
                            ->options(Expense::all()->sortBy('title')->pluck('title', 'id')->toArray())
                            ->required()
                            ->minItems(1)
                            ->bulkToggleable()
                            ->validationAttribute('Catégories de dépenses éligibles')
                            ->validationMessages([
                                'required' => 'Le champ ":attribute" est obligatoire.',
                                'min' => 'Le champ ":attribute" doit comprendre au moins :min élément.',
                            ]),
                    ])->extraAttributes(['class' => 'h-full'])
                        ->label(false)
                        ->columnSpan(1),
                    \LaraZeus\Accordion\Forms\Accordions::make('Disciplines scientifiques')
                        ->activeAccordion(2)
                        ->isolated()
                        ->accordions([
                            \LaraZeus\Accordion\Forms\Accordion::make('main-data')
                                ->columns()
                                ->label('Disciplines scientifiques')
                                ->schema($this->getFieldsetSchema()),
                        ])
                        ->columnSpanFull(),
                    Select::make('geo_zones')
                        ->label("Zones géographiques")
                        ->multiple()
                        ->nullable()
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

                            return $options;
                        })
                        ->columnSpanFull(),
                    Actions::make([
                        Action::make('nextTab')
                            ->label('Suivant')
                            ->icon('heroicon-o-arrow-right')
                            ->color('primary')
                    ])->extraAttributes([
                        '@click' => "tab = '-dates-importantes-tab'",
                        'x-bind:class' => "{ 'alpine-active': tab === '-dates-importantes-tab' }",
                    ])
                        ->alignEnd()
                        ->columnSpanFull(),
                ])->columns(2),
                Tabs\Tab::make('dates-importantes')
                    ->label('Dates importantes')
                    ->schema([
                        Fieldset::make('Deadlines')->schema([
                            Repeater::make('deadlines')->schema([
                                DatePicker::make('date')
                                    ->label('Date')
                                    ->required()
                                    ->validationAttribute('Date')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                    ]),
                                TextInput::make('proof')
                                    ->label('Justificatif')
                                    ->required()
                                    ->datalist([
                                        'Deposit of the draft',
                                        'Deposit of the letter of Intent',
                                        'Deposit of the preliminary draft',
                                        'Full-proposal',
                                        'Internal deadline',
                                        'Pre-proposal',
                                        'Promoter',
                                        'Registration',
                                        'Submission of applications'
                                    ])
                                    ->maxLength(255)
                                    ->validationAttribute('Justificatif')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                        'max' => 'Le champ ":attribute" ne peut pas excéder :max caractères de long.',
                                    ]),
                                Checkbox::make('continuous')
                                    ->default(false)
                                    ->label('Continu'),
                            ])->label(false)
                                ->addActionLabel('+ Ajouter une deadline')
                                ->minItems(1)
                                ->required()
                                ->defaultItems(1)
                                ->validationAttribute('Deadlines')
                                ->validationMessages([
                                    'required' => 'L\'appel doit contenir au moins une ":attribute".',
                                ]),
                        ]),
                        Actions::make([
                            Action::make('nextTab')
                                ->label('Suivant')
                                ->icon('heroicon-o-arrow-right')
                                ->color('primary')
                        ])->extraAttributes([
                            '@click' => "tab = '-description-tab'",
                            'x-bind:class' => "{ 'alpine-active': tab === '-description-tab' }",
                        ])
                            ->alignEnd()
                            ->columnSpanFull(),
                    ]),
                Tabs\Tab::make('description')
                    ->label('Description')
                    ->schema([
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
                            ->extraAttributes(['maxlength' => 500, 'script'])
                            ->maxLength(500)
                            ->extraAttributes(['maxlength' => 500, 'script' => ""])
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
                            ->required()
                            ->validationAttribute('Description courte')
                            ->validationMessages([
                                'required' => 'Le champ ":attribute" est obligatoire.',
                                'max' => 'Le champ ":attribute" ne peut pas excéder :max caractères de long.',
                            ]),
                        Actions::make([
                            Action::make('nextTab')
                                ->label('Suivant')
                                ->icon('heroicon-o-arrow-right')
                                ->color('primary')
                        ])->extraAttributes([
                            '@click' => "tab = '-funding-tab'",
                            'x-bind:class' => "{ 'alpine-active': tab === '-funding-tab' }",
                        ])
                            ->alignEnd()
                            ->columnSpanFull(),
                    ]),
                Tabs\Tab::make('funding')
                    ->label('Budget et dépenses')
                    ->schema([
                        TiptapEditor::make('funding')
                            ->label(false)
                            ->nullable()
                            ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                            ->maxContentWidth('full')
                            ->disableFloatingMenus()
                            ->validationAttribute("Budget et dépenses"),
                        Actions::make([
                            Action::make('nextTab')
                                ->label('Suivant')
                                ->icon('heroicon-o-arrow-right')
                                ->color('primary')
                        ])->extraAttributes([
                            '@click' => "tab = '-requirements-tab'",
                            'x-bind:class' => "{ 'alpine-active': tab === '-requirements-tab' }",
                        ])
                            ->alignEnd()
                            ->columnSpanFull(),
                    ]),
                Tabs\Tab::make("requirements")
                    ->label("Critères d'admission")
                    ->schema([
                        TiptapEditor::make('admission_requirements')
                            ->label(false)
                            ->nullable()
                            ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                            ->maxContentWidth('full')
                            ->disableFloatingMenus()
                            ->validationAttribute("Critères d'admission"),
                        Actions::make([
                            Action::make('nextTab')
                                ->label('Suivant')
                                ->icon('heroicon-o-arrow-right')
                                ->color('primary')
                        ])->extraAttributes([
                            '@click' => "tab = '-apply-instructions-tab'",
                            'x-bind:class' => "{ 'alpine-active': tab === '-apply-instructions-tab' }",
                        ])
                            ->alignEnd()
                            ->columnSpanFull(),
                    ]),
                Tabs\Tab::make("apply-instructions")
                    ->label('Pour postuler')
                    ->schema([
                        TiptapEditor::make('apply_instructions')
                            ->label(false)
                            ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                            ->maxContentWidth('full')
                            ->disableFloatingMenus()
                            ->nullable()
                            ->validationAttribute('Pour postuler'),
                        Actions::make([
                            Action::make('nextTab')
                                ->label('Suivant')
                                ->icon('heroicon-o-arrow-right')
                                ->color('primary')
                        ])->extraAttributes([
                            '@click' => "tab = '-contact-tab'",
                            'x-bind:class' => "{ 'alpine-active': tab === '-contact-tab' }",
                        ])
                            ->alignEnd()
                            ->columnSpanFull(),
                    ]),
                Tabs\Tab::make("contact")
                    ->label("Contacts")
                    ->schema([
                        Fieldset::make('Internes')->schema([
                            Repeater::make('contact_ulb')
                                ->schema([
                                    TextInput::make('first_name')
                                        ->label('Prénom')
                                        ->required()
                                        ->minLength(3)
                                        ->regex("/^[a-zA-Z0-9]+(?:[-' ][a-zA-Z0-9]+)*$/")
                                        ->validationAttribute('Prénom')
                                        ->validationMessages([
                                            'required' => 'Le prénom d\'un contact interne est obligatoire.',
                                            'min' => 'Le prénom d\'un contact interne ne doit pas faire moins de :min caractères de long.',
                                        ]),
                                    TextInput::make('last_name')
                                        ->label('Nom')
                                        ->required()
                                        ->minLength(3)
                                        ->regex("/^[a-zA-Z0-9]+(?:[-' ][a-zA-Z0-9]+)*$/")
                                        ->validationAttribute('Nom')
                                        ->validationMessages([
                                            'required' => 'Le nom d\'un contact interne est obligatoire.',
                                            'min' => 'Le nom d\'un contact interne ne doit pas faire moins de :min caractères de long.',
                                        ]),
                                    TextInput::make('email')
                                        ->label('E-mail')
                                        ->email()
                                        ->required()
                                        ->minLength(5)
                                        ->validationAttribute('E-mail')
                                        ->validationMessages([
                                            'required' => 'L\'adresse e-mail d\'un contact interne est obligatoire.',
                                            'min' => 'L\'adresse e-mail d\'un contact interne ne doit pas faire moins de :min caractères de long.',
                                            'email' => 'L\'adresse e-mail d\'un contact interne n\'est pas valide.',
                                        ]),
                                ])
                                ->columns(2)
                                ->addActionLabel('+ Nouveau contact')
                                ->label(false)
                                ->maxItems(3)
                                ->requiredWithout('contact_ext')
                                ->validationAttribute('Contact interne')
                                ->validationMessages([
                                    'required_without' => 'Veuillez renseigner au moins un contact interne ou externe.',
                                ]),
                        ]),
                        Fieldset::make('Externes')->schema([
                            Repeater::make('contact_ext')
                                ->schema([
                                    TextInput::make('first_name')
                                        ->label('Prénom')
                                        ->required()
                                        ->minLength(3)
                                        ->regex("/^[a-zA-Z0-9]+(?:[-' ][a-zA-Z0-9]+)*$/")
                                        ->validationAttribute('Prénom externe')
                                        ->validationMessages([
                                            'required' => 'Le prénom d\'un contact externe est obligatoire.',
                                            'min' => 'Le prénom d\'un contact externe ne doit pas faire moins de :min caractères de long.',
                                        ]),
                                    TextInput::make('last_name')
                                        ->label('Nom')
                                        ->required()
                                        ->minLength(3)
                                        ->regex("/^[a-zA-Z0-9]+(?:[-' ][a-zA-Z0-9]+)*$/")
                                        ->validationAttribute('Nom externe')
                                        ->validationMessages([
                                            'required' => 'Le nom d\'un contact externe est obligatoire.',
                                            'min' => 'Le nom d\'un contact externe ne doit pas faire moins de :min caractères de long.',
                                        ]),
                                    TextInput::make('email')
                                        ->label('E-mail')
                                        ->email()
                                        ->required()
                                        ->minLength(5)
                                        ->validationAttribute('E-mail externe')
                                        ->validationMessages([
                                            'required' => 'L\'adresse e-mail d\'un contact externe est obligatoire.',
                                            'min' => 'L\'adresse e-mail d\'un contact externe ne doit pas faire moins de :min caractères de long.',
                                            'email' => 'L\'adresse e-mail d\'un contact externe n\'est pas valide.',
                                        ]),
                                ])
                                ->columns(2)
                                ->addActionLabel('+ Nouveau contact')
                                ->label(false)
                                ->maxItems(3)
                                ->requiredWithout('contact_ulb')
                                ->validationAttribute('Contact externe')
                                ->validationMessages([
                                    'required_without' => 'Veuillez renseigner au moins un contact interne ou externe.',
                                ]),
                        ]),
                        Actions::make([
                            Action::make('nextTab')
                                ->label('Suivant')
                                ->icon('heroicon-o-arrow-right')
                                ->color('primary')
                        ])->extraAttributes([
                            '@click' => "tab = '-files-tab'",
                            'x-bind:class' => "{ 'alpine-active': tab === '-files-tab' }",
                        ])
                            ->alignEnd()
                            ->columnSpanFull(),
                    ])
                    ->reactive(),
                Tabs\Tab::make('files')
                    ->label('Documents')
                    ->schema([
                        FileUpload::make('documents')
                            ->label('Documents')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(15000)
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
                                'application/vnd.oasis.opendocument.text', // ODT
                                'application/msword', // DOC
                                'text/plain', // TXT
                                'image/png', // PNG
                                'image/jpeg', // JPG, JPEG
                                'image/svg+xml', // SVG
                                'application/vnd.ms-excel', // XLS
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // XLSX
                                'application/vnd.ms-powerpoint', // PPT
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation', // PPTX
                            ])
                            ->multiple()
                            ->moveFiles()
                            ->default(fn() => $this->project->documents->pluck('path')->toArray()),
                        Actions::make([
                            Action::make('nextTab')
                                ->label('Suivant')
                                ->icon('heroicon-o-arrow-right')
                                ->color('primary')
                        ])->extraAttributes([
                            '@click' => "tab = '-sessions-tab'",
                            'x-bind:class' => "{ 'alpine-active': tab === '-sessions-tab' }",
                        ])
                            ->alignEnd()
                            ->columnSpanFull(),
                    ]),
                Tabs\Tab::make('sessions')
                    ->label("Séances d'information")
                    ->live()
                    ->schema([
                        Select::make('infos_sessions')
                            ->label('Séances d\'info')
                            ->relationship('info_sessions', 'title')
                            ->multiple()
                            ->searchable()
                            ->nullable()
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
                                    ->validationAttribute('Titre')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                        'string' => 'Le champ ":attribute" doit être une chaîne de caractères.',
                                    ]),
                                RichEditor::make('description')
                                    ->toolbarButtons(['underline', 'italic', 'bold'])
                                    ->label('Description')
                                    ->required()
                                    ->string()
                                    ->extraAttributes(['style' => 'max-height: 200px'])
                                    ->columnSpanFull()
                                    ->validationAttribute('Description')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                        'string' => 'Le champ ":attribute" doit être une chaîne de caractères.',
                                    ]),
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
                                    ->after('today')
                                    ->columnSpan(1)
                                    ->required()
                                    ->validationAttribute('Date et heure')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                        'after' => 'Le champ ":attribute" doit avoir une valeur ultérieure à la date du jour.',
                                    ]),
                                TextInput::make('speaker')
                                    ->label('Présentateur·ice')
                                    ->string()
                                    ->nullable()
                                    ->columnSpan(1)
                                    ->validationAttribute('Présentateur·ice')
                                    ->validationMessages([
                                        'string' => 'Le champ ":attribute" doit être une chaîne de caractères.',
                                    ]),
                                Select::make('session_type')
                                    ->label('Type de session')
                                    ->options([
                                        2 => 'Hybride',
                                        1 => 'Présentiel',
                                        0 => 'Distanciel',
                                    ])
                                    ->default(2)
                                    ->reactive()
                                    ->required()
                                    ->validationAttribute('Type de session')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                    ]),
                                TextInput::make('url')
                                    ->required()
                                    ->label('URL de la réunion')
                                    ->visible(fn($get) => in_array($get('session_type'), [0, 2]))
                                    ->url()
                                    ->activeUrl()
                                    ->reactive()
                                    ->validationAttribute('URL de la réunion')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                        'activeUrl' => 'L\'URL renseignée n\'est pas jugée sûre.',
                                        'url' => 'Le champ ":attribute" doit être un URL valide.',
                                    ]),
                                TextInput::make('location')
                                    ->required()
                                    ->string()
                                    ->label('Adresse')
                                    ->visible(fn($get) => in_array($get('session_type'), [1, 2]))
                                    ->validationAttribute('Adresse')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                        'string' => 'Le champ ":attribute" doit être une chaîne de caractères.',
                                    ]),
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
                                    ->validationAttribute('Organisation')
                                    ->validationMessages([
                                        'required' => 'Le champ ":attribute" est obligatoire.',
                                    ]),
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
                    ->label('Valider les modifications')
                    ->color('primary')
                    ->icon('heroicon-o-check')
                    ->action('$set("showModal", true)'),
                Action::make('saveAsDraft')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->label('Garder en brouillon')
                    ->color('info')
                    ->action('saveAsDraft'),
                Action::make('copy')
                    ->icon('heroicon-o-document-duplicate')
                    ->label('Dupliquer')
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

    protected function onValidationError(ValidationException $exception): void
    {
        foreach ($exception->errors() as $error) {
            foreach ($error as $e) {
                Notification::make()
                    ->title($e)
                    ->warning()
                    ->icon('heroicon-o-exclamation-circle')
                    ->color('warning')
                    ->iconColor('warning')
                    ->seconds(5)
                    ->send();
            }

        }

    }

    public function submit(): void
    {
        if (!$this->fileService) {
            $this->fileService = app(FileService::class);
        }
        $userId = Auth::id();
        /*
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
                    'origin_url' => 'string|nullable',
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
                    'short_description' => 'Description courte',
                    'long_description' => 'Description longue',
                    'funding' => 'Budget et dépenses',
                    'admission_requirements' => 'Critères d\'admission',
                    'apply_instructions' => 'Pour postuler',
                    'origin_url' => 'L\'url d\'origine',
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
        */
        if ($this->form->validate()) {
            $data = $this->data;

            try {
                $data['last_update_user_id'] = $userId;
                /* REMOVED causait des problèmes d'interprétation de l'éditeur lors de la modification BLABLOU
                $converter = new HtmlConverter();
                $markdown = $converter->convert($this->data["short_description"]);

                $data['short_description'] = $markdown;.
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

                $data["status"] = 1;

                $data['is_in_next_email'] = $this->isInNextEmail ? 1 : 0;

                $this->project->update($data);

                if ($this->isInNextEmail) {
                    $this->project->touch();
                }

                $this->project->expenses()->sync($data['expenses'] ?? []);
                $this->project->activities()->sync($data['activities'] ?? []);
                $this->project->scientific_domains()->sync($data['scientific_domains'] ?? []);
                $this->project->info_sessions()->sync($data['infos_sessions'] ?? []);

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

                $this->project->last_update_user_id = Auth::id();
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

    public function copyProject()
    {
        $project = $this->replicateModelWithRelations($this->project);

        Notification::make()->title('Le projet a été copié avec succès.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();

        /***
         * 03/02/2025
         *
         * Redirection vers Edit de projet lors d'une duplication
         *
         * L'utilisateur n'a aucune obligation de modifier le projet pour qu'il soit enregistré, mais la redirection les forcera peut-être à remettre la fiche aux normes ?
         * Si aucune amélioration n'est constatée, trouver autre solution plus contraignante.
         *
         */
        Log::alert(" [{$project->id}] Projet dupliqué !");
        return redirect()->route('projects.edit', $project->id);
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
                    session()->flash('defaultTab', 'drafts');
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
            
            /*
            session()->flash('defaultTab', 'drafts');
            redirect()->route('profile.show');
            */

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
