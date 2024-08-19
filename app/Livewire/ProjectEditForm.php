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

    public function render()
    {
        return view('livewire.project-edit-form');
    }

    public function archiveProject()
    {
        $this->project->update(['status' => -1]);
        session()->flash('success', "Le projet a été supprimé avec succès.");
        return redirect()->route('projects.index');
    }


    public function mount(Project $project)
    {
        $this->project = $project->load('organisations', 'scientific_domains', 'info_types', 'country', 'continent', 'documents');

        $this->project->contact_ulb = $this->transformContacts($this->project->contact_ulb);
        $this->project->contact_ext = $this->transformContacts($this->project->contact_ext);

        $this->checkAndAddOrganisation($this->project->Organisation);

        $this->countries = Countries::all()->pluck('nomPays', 'id')->toArray();
        $this->continents = Continent::all()->pluck('name', 'id')->toArray();

        $geo_zones = [];
        if ($this->project->country_id) {
            $geo_zones[] = 'country_' . $this->project->country_id;
        }
        if ($this->project->continent_id) {
            $geo_zones[] = 'continent_' . $this->project->continent_id;
        }

        if ($this->project->SeanceFin == 1) {
            $this->project->info = "Financement";
        }

        $documents = $this->project->documents->pluck('filename')->toArray();

        $this->originalDocuments = $documents;

        $data = array_merge(
            $this->project->toArray(),
            [
                'scientific_domains' => $this->project->scientific_domains->pluck('id')->toArray(),
                'geo_zones' => $geo_zones,
                'documents' => $documents
            ]
        );

        $this->id = $data["id"];

        $this->form->fill($data);
    }

    private function checkAndAddOrganisation($organisationName)
    {
        if (!$organisationName) {
            return;
        }

        $organisation = Organisation::firstOrCreate(['title' => $organisationName]);

        if (!$this->project->organisations->contains($organisation->id)) {
            $this->project->organisations()->attach($organisation->id);
        }
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
                    Select::make('organisation')
                        ->searchable()
                        ->createOptionForm([
                            TextInput::make('title')
                                ->required()
                        ])
                        ->label('Organisation')
                        ->required()
                        ->relationship('organisations', 'title')
                        ->options(Organisation::all()->pluck('title', 'id')->toArray()),
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
                                $options["country_$id"] = $name;
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
                        ->acceptedFileTypes(['application/pdf'])
                        ->multiple()
                        ->moveFiles()
                        ->default(fn() => $this->project->documents->pluck('filename')->toArray())
                ])
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
        $userId = Auth::id();

        $rules = [
            'title' => 'required|string|max:255',
            'is_big' => 'boolean',
            'organisation' => 'array',
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
        ]);


        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::make()->color('danger')->icon('heroicon-o-x-circle')->seconds(5)->send()->title($error);
            }
        } else {
            $data = $validator->validated();
        }

        try {

            $data['last_update_user_id'] = $userId;

            $converter = new HtmlConverter();
            $markdown = $converter->convert($this->data["short_description"]);

            $data['short_description'] = $markdown;

            if ($data['periodicity'] === null) {
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
            }
            $this->project->update($data);

            $this->project->organisations()->sync($data['organisation'] ?? null);
            $this->project->info_types()->sync($data['info_types'] ?? []);
            $this->project->scientific_domains()->sync($data['scientific_domains'] ?? []);

            if (isset($data['documents'])) {
                $this->handleDocumentUpdates($data['documents'], $this->project);
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
            Notification::make()->title('Le projet a été modifié avec success.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();
            redirect()->route('projects.index');
        } catch (\Exception $e) {
            Notification::make()->title("Le projet n'a pas pu être modifié.")->icon('heroicon-o-x-circle')->seconds(5)->color('danger')->send();

            redirect()->route('projects.index');
        }
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
        }

        // Gérer le cas où la sauvegarde du nouveau brouillon échoue
        redirect()->back()->withErrors('La sauvegarde du brouillon a échoué.');
    }

    private function handleDocumentUpdates(array $newDocuments, Project $project)
    {
        $existingDocuments = $project->documents->pluck('filename')->toArray();

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
                'title' => $file->getClientOriginalName(),
                'filename' => $finalPath,
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
