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
use App\Services\FileService;
use App\Traits\ScientificDomainSchemaTrait;
use Filament\Forms\ComponentContainer;
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
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use League\HTMLToMarkdown\HtmlConverter;
use Livewire\Component;

final class ProjectForm extends Component implements HasForms
{
    use InteractsWithForms, ScientificDomainSchemaTrait;

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

        // Initialisation des données du formulaire
        $this->form->fill($this->project->toArray());

        // Assurons-nous que infos_sessions est initialisé comme un tableau vide
        if (!isset($this->data['infos_sessions'])) {
            $this->data['infos_sessions'] = [];
        }

        $this->refreshInfoSessionsOptions();
    }

    public function refreshInfoSessionsOptions()
    {
        $this->infoSessionsOptions = cache()->remember('info_sessions_future', 3600, function () {
            return InfoSession::where('session_datetime', '>', now())
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->id => $item->id . ' - ' . $item->title];
                })
                ->toArray();
        });
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

        foreach (['organisation', 'scientific_domains', 'geo_zones', 'documents', 'document_filenames', 'info_sessions', 'expenses', 'activities'] as $attribute) {
            if (isset($data[$attribute])) {
                if ($attribute == 'organisation') {
                    $this->project->{$attribute} = $data[$attribute];
                } else {
                    $this->project->{$attribute} = $data[$attribute];
                }
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Tabs')
                ->reactive()
                ->live()
                ->tabs([
                    Tabs\Tab::make('infos')
                        ->label('Informations')
                        ->schema([
                            TextInput::make('title')
                                ->label('Titre')
                                ->placeholder('Titre concis, évitez les acronymes seuls')
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
                                        ->helperText('Veuillez éviter les abbréviations seules.')
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
                                    ->label(new HtmlString("<strong>Catégories d'activité</strong>"))
                                    ->options(fn() => cache()->remember('activities_form_list', 86400, function () {
                                        return Activity::all()->sortBy('title')->pluck('title', 'id')->toArray();
                                    }))
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
                                    ->label(new HtmlString("<strong>Catégories de dépenses éligibles</strong>"))
                                    ->options(fn() => cache()->remember('expenses_form_list', 86400, function () {
                                        return Expense::all()->sortBy('title')->pluck('title', 'id')->toArray();
                                    }))
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
                                ->options(fn() => cache()->remember('geo_zones_list', 86400, function () {
                                    $options = ['Monde entier' => 'Monde entier'];
                                    $continents = Continent::all()->pluck('name', 'code')->toArray();
                                    $pays = Country::all()->pluck('name', 'id')->toArray();
                                    
                                    foreach ($continents as $code => $name) {
                                        $options["continent_$code"] = $name;
                                    }
                                    
                                    foreach ($pays as $id => $name) {
                                        $options["pays_$id"] = $name;
                                    }
                                    
                                    return $options;
                                }))
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
                        ->id('dates-importantes')
                        ->label('Dates importantes')
                        ->schema([
                            Fieldset::make('Deadlines')->schema([
                                Repeater::make('deadlines')->schema([
                                    DatePicker::make('date')
                                        ->label('Date')
                                        ->after('today')
                                        ->required()
                                        ->validationAttribute('Date')
                                        ->validationMessages([
                                            'required' => 'Le champ ":attribute" est obligatoire.',
                                            'after' => 'Le champ ":attribute" doit avoir une valeur ultérieure à la date du jour.',
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
                                ->extraAttributes(['className' => "limited-trix"])
                                ->maxLength(500)
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
                                ->reactive()
                                ->validationAttribute('Description courte')
                                ->validationMessages([
                                    'required' => 'Le champ ":attribute" est obligatoire.',
                                    'max' => 'Le champ ":attribute" ne peut pas excéder :max caractères de long.',
                                ]),
                            TiptapEditor::make('long_description')
                                ->profile('default')
                                ->output(TiptapOutput::Json)
                                ->columnSpan(1)
                                ->maxContentWidth('full')
                                ->label('Description complète')
                                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                                ->disableFloatingMenus()
                                ->placeholder('Description la plus complète possible du projet, aucune limite de caractères')
                                ->required()
                                ->validationAttribute('Description complète')
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
                                ->output(TiptapOutput::Json)
                                ->maxContentWidth('full')
                                ->disableFloatingMenus()
                                ->placeholder('Informations sur le montant du financement, sa durée, etc. (Vous pouvez laisser ce champ vide)')
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
                        ])
                    ,
                    Tabs\Tab::make("requirements")
                        ->label("Critères d'admission")
                        ->schema([
                            TiptapEditor::make('admission_requirements')
                                ->label(false)
                                ->nullable()
                                ->output(TiptapOutput::Json)
                                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                                ->maxContentWidth('full')
                                ->disableFloatingMenus()
                                ->placeholder("Informations sur les prérequis pour l'admission. (Vous pouvez laisser ce champ vide)")
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
                    Tabs\Tab::make("apply_instructions")
                        ->label("Pour postuler")
                        ->schema([
                            TiptapEditor::make('apply_instructions')
                                ->nullable()
                                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                                ->maxContentWidth('full')
                                ->disableFloatingMenus()
                                ->label(false)
                                ->placeholder("Informations sur la marche à suivre pour candidater au projet/prix. (Vous pouvez laisser ce champ vide)")
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
                        ->label('Contacts')
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
                                ->moveFiles(),
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
                                ->options(fn() => $this->infoSessionsOptions)
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
                                        $set('infos_sessions', [$info_session->id]);

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
        ])
            ->statePath('data')
            ->model($this->project);
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

                session()->flash('defaultTab', 'drafts');
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

            session()->flash('defaultTab', 'drafts');
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
        if ($this->form->validate()) {
            $data = $this->data;
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

                if (!empty($data['expenses'])) {
                    $project->expenses()->sync($data['expenses']);
                }

                if (!empty($data['activities'])) {
                    $project->activities()->sync($data['activities']);
                }

                if (!empty($data['scientific_domains'])) {
                    $project->scientific_domains()->sync($data['scientific_domains']);
                }

                if (!empty($data['infos_sessions'])) {
                    // Filtrer les valeurs null de infos_sessions
                    $validSessions = array_filter($data['infos_sessions'], function ($session) {
                        return $session !== null;
                    });

                    // Synchroniser uniquement les sessions valides
                    if (!empty($validSessions)) {
                        $project->info_sessions()->sync($validSessions);
                    }
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
