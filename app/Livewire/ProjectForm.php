<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Countries;
use App\Models\Document;
use App\Models\Draft;
use App\Models\InfoType;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ScientificDomainCategory;
use App\Services\FileService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
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
                $document = Document::where('path', $docPath)->first();
                return $document ? $document->filename : basename($docPath);
            }, $data['documents']);
        }

        foreach (['organisation', 'scientific_domains', 'info_types', 'geo_zones', 'documents', 'document_filenames'] as $attribute) {
            if (isset($data[$attribute])) {
                if ($attribute == 'organisation') {
                    $this->project->{$attribute} = $data[$attribute][0];
                } else {
                    $this->project->{$attribute} = $data[$attribute];
                }
            }
        }
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
                    Select::make('organisation')
                        ->searchable()
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
                    TextInput::make('origin_url')
                        ->label('Lien vers l\'appel original')
                        ->url(),
                    Select::make('info')
                        ->label("Type d'information")
                        ->options([
                            'Financement',
                            "Séance d'information organisée par l'ULB",
                            "Séance d'information organisée par un organisme externe"
                        ])
                        ->selectablePlaceholder()
                        ->required(),
                    CheckboxList::make('info_types')
                        ->label('Types de programmes')
                        ->options(InfoType::all()->sortBy('title')->pluck('title', 'id')->toArray())
                        ->columns(3)
                        ->required(),
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
                            Checkbox::make('continuous')->default(false),
                        ])->label(false)->addActionLabel('+ Ajouter une deadline')->minItems(1)->required()->defaultItems(1),
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
                        ->acceptedFileTypes(['application/pdf'])
                        ->multiple()
                        ->moveFiles(),
                ]),
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
        if (!$this->fileService) {
            $this->fileService = app(FileService::class);
        }
        if (!empty($this->data['documents'])) {
            $docs = $this->fileService->moveForDraft($this->data['documents'], $this->draft->content["documents"]);
            $this->data["documents"] = $docs;
        }
        if ($this->draft) {
            $updatedDraft = Draft::find($this->draft->id);

            if ($updatedDraft) {
                $updateSuccessful = $updatedDraft->update([
                    'content' => $this->data,
                    'poster_id' => Auth::id()
                ]);

                if ($updateSuccessful) {
                    Notification::make()
                        ->title("Brouillon enregistré")
                        ->color('success')
                        ->seconds(5)
                        ->send();
                    return redirect()->route('profile.show');
                }
            }
        }

        $draft = new Draft([
            'content' => $this->data,
            'poster_id' => Auth::id()
        ]);

        if ($draft->save()) {
            Notification::make()->title('Brouillon enregistré.')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->send();
            redirect()->route('profile.show');
        }

        // Gérer le cas où la sauvegarde du nouveau brouillon échoue
        Notification::make()
            ->title("La sauvegarde du brouillon a échoué")
            ->color('danger')
            ->seconds(5)
            ->send();
        return redirect()->back();
    }

    public function preview()
    {
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
            'organisation' => 'string',
            'info_types' => 'array',
            'documents' => 'array',
            'scientific_domains' => 'array',
            'geo_zones' => 'array',
            'deadlines' => 'array',
            'periodicity' => 'nullable|integer',
            'date_lessor' => 'nullable|date',
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
        ];

        $validator = Validator::make($this->data, $rules, [], [
            'title' => 'Titre',
            'is_big' => 'Projet Majeur',
            'organisation' => 'Organisation',
            'info_types' => 'Types de programme',
            'scientific_domains' => 'Disciplines scientifiques',
            'geo_zones' => 'Zones géographiques',
            'deadlines' => 'Deadlines',
            'periodicity' => 'Périodicité',
            'date_lessor' => 'Date Bailleur',
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
                $data['contact_ulb'] = !empty($contactsUlB) ? json_encode($contactsUlB) : [];
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
                $data['contact_ext'] = !empty($contactsExt) ? json_encode($contactsExt) : [];
            } else {
                $data['contact_ext'] = [];
            }

            if ($project = Project::create($data)) {
                if (!empty($data['organisation'])) {
                    $project->organisations()->sync($data['organisation']);
                }

                if (!empty($data['info_types'])) {
                    $project->info_types()->sync($data['info_types']);
                }

                if (!empty($data['scientific_domains'])) {
                    $project->scientific_domains()->sync($data['scientific_domains']);
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
