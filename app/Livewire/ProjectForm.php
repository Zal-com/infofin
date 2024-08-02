<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Countries;
use App\Models\Draft;
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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Livewire\Component;

final class ProjectForm extends Component implements HasForms
{
    use InteractsWithForms;

    public $draft;
    public Project $project;
    public array $data = [];
    public $fromPrev;

    public function mount(Project $project = null)
    {
        if (session()->has('fromPreviewData')) {
            $this->fromPrev = session('fromPreviewData');
            $this->project = new Project($this->fromPrev);
            if (isset($this->fromPrev['organisation'])) {
                $this->project->organisation = $this->fromPrev['organisation'];
            }
            if (isset($this->fromPrev['scientific_domains'])) {
                $this->project->scientific_domains = $this->fromPrev['scientific_domains'];
            }
            if (isset($this->fromPrev['info_types'])) {
                $this->project->info_types = $this->fromPrev['info_types'];
            }
            if (isset($this->fromPrev['Geo_zones'])) {
                $this->project->Geo_zones = $this->fromPrev['Geo_zones'];
            }
            $this->form->fill($this->project->toArray());
        } else {
            if ($this->draft) {
                $this->project = new Project(json_decode($this->draft->content, true));
            } else {
                $this->project = $project ?? new Project();
            }

            $this->form->fill($this->project->toArray());
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
                    Fieldset::make('Deadlines')->schema([
                        Repeater::make('deadlines')->schema([
                            DatePicker::make('date')->label('Date'),
                            TextInput::make('proof')->label('Justificatif'),
                            Checkbox::make('continuous')->default(false),
                        ])->label(false)->addActionLabel('+ Ajouter une deadline')->minItems(1)->required()->defaultItems(1),
                    ]),

                    /*
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
                    */
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
                        ->placeholder('Courte et catchy, elle sera visible depuis la page principale et dans la newsletter')
                        ->live()
                        ->required(),
                    MarkdownEditor::make('long_description')
                        ->label('Description complète')
                        ->placeholder('Description la plus complète possible du projet, aucune limite de caractères')
                        ->hint(new HtmlString('Ce champ supporte la syntaxe MarkDown. <a target="blank_" href="https://www.markdownguide.org/cheat-sheet/" style="text-decoration: underline">Comment faire la mise en forme ? <i class="fa fa-solid fa-arrow-up-right-from-square fa-xs"></i></a>'))
                        ->required(),
                ]),
                Tabs\Tab::make('Financement')->schema([
                    MarkdownEditor::make('funding')
                        ->hint("Informations sur le financement et le budget de l'appel")
                        ->label("Financement")
                        ->required()
                        ->hint(new HtmlString('Ce champ supporte la syntaxe MarkDown. <a target="blank_" href="https://www.markdownguide.org/cheat-sheet/" style="text-decoration: underline">Comment faire la mise en forme ? <i class="fa fa-solid fa-arrow-up-right-from-square fa-xs"></i></a>'))
                        ->placeholder('Informations sur le montant du financement, sa durée, etc.'),
                ]),
                Tabs\Tab::make("Critères d'admission")->schema([
                    MarkdownEditor::make('admission_requirements')
                        ->label("Critères d'admission")
                        ->hint(new HtmlString('Ce champ supporte la syntaxe MarkDown. <a target="blank_" href="https://www.markdownguide.org/cheat-sheet/" style="text-decoration: underline">Comment faire la mise en forme ? <i class="fa fa-solid fa-arrow-up-right-from-square fa-xs"></i></a>'))
                        ->required(),
                ]),
                Tabs\Tab::make("Pour postuler")->schema([
                    MarkdownEditor::make('apply_instructions')
                        ->hint(new HtmlString('Ce champ supporte la syntaxe MarkDown. <a target="blank_" href="https://www.markdownguide.org/cheat-sheet/" style="text-decoration: underline">Comment faire la mise en forme ? <i class="fa fa-solid fa-arrow-up-right-from-square fa-xs"></i></a>'))
                        ->label("Pour postuler")
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
                    FileUpload::make('docs')
                        ->label('Documents')
                        ->multiple()
                        ->disk('public')
                        ->visibility('public')
                        ->directory('uploads/docs')]),
            ]),
        ])->statePath('data')->model($this->project);
    }

    public function render()
    {
        return view('livewire.project-form');
    }

    public function saveAsDraft()
    {
        if ($this->draft) {
            $updatedDraft = Draft::find($this->draft->id);

            if ($updatedDraft) {
                $updateSuccessful = $updatedDraft->update([
                    'content' => json_encode($this->data),
                    'poster_id' => Auth::id()
                ]);

                if ($updateSuccessful) {
                    return redirect()->route('profile.show')->with('success', 'Le brouillon a bien été enregistré.');
                }
            }
        }

        $draft = new Draft([
            'content' => json_encode($this->data),
            'poster_id' => Auth::id()
        ]);

        if ($draft->save()) {
            return redirect()->route('profile.show')->with('success', 'Brouillon enregistré');
        }

        // Gérer le cas où la sauvegarde du nouveau brouillon échoue
        return redirect()->back()->withErrors('La sauvegarde du brouillon a échoué.');
    }

    public function preview()
    {
        session()->flash('previewData', $this->data);
        return redirect()->route('projects.preview');
    }

    public function submit()
    {
        dd($this->data);
        $userId = Auth::id();

        $rules = [
            'title' => 'required|string|max:255',
            'is_big' => 'boolean',
            'organisation' => 'array',
            'info_types' => 'array',
            'docs' => 'array',
            'scientific_domains' => 'array',
            'Geo_zones' => 'array',
            'deadlines' => 'array',
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
            'Geo_zones' => 'Zones géographiques',
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
            session()->flash('error', $validator->errors()->all());
            return redirect()->back()->withInput();
        } else {
            $data = $validator->validated();
        }

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
            $data['contact_ulb'] = !empty($contactsUlB) ? json_encode($contactsUlB) : '[]';
        } else {
            $data['contact_ulb'] = '[]';
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
            $data['contact_ext'] = !empty($contactsExt) ? json_encode($contactsExt) : '[]';
        } else {
            $data['contact_ext'] = '[]';
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

            if (isset($data['docs']) && count($data['docs']) > 0) {
                $data['docs'] = $this->moveFiles($data['docs']);
            }

            if (!empty($data['Geo_zones'])) {
                foreach ($data['Geo_zones'] as $zone) {
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
            return redirect()->route('projects.index')->with('success', 'Votre appel a bien été ajouté.');
        }
    }

    private function moveFiles(array $files): array
    {
        $movedFiles = [];
        foreach ($files as $file) {
            // Define the final path for the file
            $finalPath = 'uploads/docs/' . $file->getFilename();

            // Move the file from the temporary location to the final location
            Storage::disk('public')->putFileAs(
                'uploads/docs',
                $file,
                $file->getFilename()
            );

            // Add the final path to the movedFiles array
            $movedFiles[] = $finalPath;

            // Delete the temporary file
            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }
        }

        return $movedFiles;
    }

}
