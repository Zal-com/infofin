<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Countries;
use App\Models\Document;
use App\Models\Draft;
use App\Models\InfoType;
use App\Models\Project;
use App\Models\ScientificDomainCategory;
use App\Models\InfoSession;
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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\HTMLToMarkdown\HtmlConverter;
use Livewire\Component;

class ProjectEditForm extends Component implements HasForms
{
    use InteractsWithForms;

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

        $this->project = $project->load('scientific_domains', 'info_types', 'country', 'continent', 'documents');

        $this->project->contact_ulb = $this->transformContacts($this->project->contact_ulb);
        $this->project->contact_ext = $this->transformContacts($this->project->contact_ext);

        $this->countries = Countries::all()->pluck('nomPays', 'id')->toArray();
        $this->continents = Continent::all()->pluck('name', 'id')->toArray();

        $geo_zones = [];
        if ($this->project->country_id) {
            $geo_zones[] = 'pays_' . $this->project->country_id;
        }
        if ($this->project->continent_id) {
            $geo_zones[] = 'continent_' . $this->project->continent_id;
        }

        $documents = $this->project->documents->pluck('path')->toArray();
        $this->originalDocuments = $documents;

        $data = array_merge(
            $this->project->toArray(),
            [
                'scientific_domains' => $this->project->scientific_domains->pluck('id')->toArray(),
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
                'tel' => $contact['phone'] ?? '',
                'address' => $contact['address'] ?? '',
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
                    CheckboxList::make('info_types')
                        ->label('Types de programmes')
                        ->options(InfoType::all()->sortBy('title')->pluck('title')->toArray())
                        ->columns(3)
                        ->required()
                        ->relationship('info_types', 'title'),
                    Select::make('scientific_domains')
                        ->label("Disciplines scientifiques de l'appel")
                        ->multiple()
                        ->required()
                        ->relationship('scientific_domains', 'name')
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

                            $continents = $this->continents;
                            $countries = $this->countries;

                            foreach ($continents as $id => $name) {
                                $options["continent_$id"] = $name;
                            }

                            foreach ($countries as $id => $name) {
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
                Tabs\Tab::make('Documents')->schema([
                    FileUpload::make('documents')
                        ->label('Documents')
                        ->disk('public')
                        ->visibility('public')
                        ->maxSize(20)
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
                        ->options(InfoSession::all()->pluck('title', 'id')->toArray())
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
            'info_types' => 'array',
            'documents' => 'array',
            'scientific_domains' => 'array',
            'geo_zones' => 'array',
            'deadlines' => 'array',
            'periodicity' => 'nullable|integer',
            'date_lessor' => 'nullable|date',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'nullable|array',
            'funding' => 'nullable|array',
            'admission_requirements' => 'nullable|array',
            'apply_instructions' => 'nullable|array',
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

        $validator = Validator::make($this->data, $rules, [], [
            'title' => 'Titre',
            'is_big' => 'Projet Majeur',
            'organisation_id' => 'Organisation',
            'info_types' => 'Types de programme',
            'scientific_domains' => 'Disciplines scientifiques',
            'geo_zones' => 'Zones géographiques',
            'deadlines' => 'Deadlines',
            'periodicity' => 'Périodicité',
            'date_lessor' => 'Date Bailleur',
            'short_description' => 'Description courte',
            'long_description' => 'Description longue',
            'funding' => 'Financement',
            'admission_requirements' => 'Requis d\'admission',
            'apply_instructions' => 'Instructions d\'application',
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
            'info_sessions' => 'Séances d\'information',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::make()->color('danger')->icon('heroicon-o-x-circle')->seconds(5)->send()->title($error);
            }
        } else {
            $data = $validator->validated();
            try {
                $data['last_update_user_id'] = $userId;

                $converter = new HtmlConverter();
                $markdown = $converter->convert($this->data["short_description"]);

                $data['short_description'] = $markdown;

                if (!array_key_exists('periodicity', $data) || $data['periodicity'] === null) {
                    $data['periodicity'] = 0;
                }

                if (isset($data['contact_ulb'])) {
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
                    $data['contact_ext'] = $contactsExt;
                } else {
                    $data['contact_ext'] = [];
                }

                $this->project->update($data);

                $this->project->info_types()->sync($data['info_types'] ?? []);
                $this->project->scientific_domains()->sync($data['scientific_domains'] ?? []);
                $this->project->info_sessions()->sync($data['info_sessions'] ?? []);
                
                if (!empty($data['documents'])) {
                    $this->fileService->handleDocumentUpdates($data['documents'], $this->project);
                }

                if (!empty($data['geo_zones'])) {
                    foreach ($data['geo_zones'] as $zone) {
                        if (strpos($zone, 'continent_') === 0) {
                            $continent_id = str_replace('continent_', '', $zone);
                            $this->project->continent()->associate($continent_id);
                        } elseif (strpos($zone, 'pays_') === 0) {
                            $country_id = str_replace('pays_', '', $zone);
                            $this->project->country()->associate($country_id);
                        }
                    }
                }

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
        $model->load('scientific_domains', 'info_types', 'country', 'continent', 'documents');

        $newModel = $model->replicate();
        $newModel->save();

        $newModel->scientific_domains()->sync($model->scientific_domains->pluck('id')->toArray());
        $newModel->info_types()->sync($model->info_types->pluck('id')->toArray());

        foreach ($model->documents as $document) {
            $newDocument = $document->replicate();
            $newDocument->project_id = $newModel->id;
            $newDocument->save();
        }

        $newModel->country()->associate($model->country);
        $newModel->continent()->associate($model->continent);
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
        if ($this->draft) {
            $updatedDraft = Draft::find($this->draft->id);

            if ($updatedDraft) {
                $updateSuccessful = $updatedDraft->update([
                    'content' => $this->data,
                    'poster_id' => Auth::id()
                ]);

                if ($updateSuccessful) {
                    redirect()->route('profile.show')->with('success', 'Le brouillon a bien été enregistré.');
                }
            }
        }

        $draft = new Draft([
            'content' => $this->data,
            'poster_id' => Auth::id()
        ]);

        if ($draft->save()) {
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
