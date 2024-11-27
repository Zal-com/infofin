<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Activity;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Expense;
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
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $label = 'Projets';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $activeNavigationIcon = 'heroicon-s-newspaper';

    public static function form(Form $form): Form
    {
        return $form
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
                        ->options(Activity::all()->sortBy('title')->pluck('title', 'id')->toArray())
                        ->required()
                        ->bulkToggleable()
                        ->minItems(1)
                        ->relationship('activities', 'title')
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
                        ->options(Expense::all()->sortBy('title')->pluck('title', 'id')->toArray())
                        ->required()
                        ->minItems(1)
                        ->bulkToggleable()
                        ->relationship('expenses', 'title')
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
                            ->schema(static::getFieldsetSchema()),
                    ])
                    ->columnSpanFull(),
                Select::make('countries')
                    ->label('Pays')
                    ->multiple()
                    ->nullable()
                    ->maxItems(3)
                    ->relationship('countries', 'name')
                    ->options(Country::all()->pluck('name', 'id')->toArray()),
                Select::make('continents')
                    ->label('Continents')
                    ->multiple()
                    ->maxItems(3)
                    ->relationship('continents', 'name')
                    ->options(Continent::all()->pluck('name', 'id')->toArray()),
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
                TiptapEditor::make('funding')
                    ->label(false)
                    ->nullable()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->output(TiptapOutput::Json)
                    ->maxContentWidth('full')
                    ->disableFloatingMenus()
                    ->placeholder('Informations sur le montant du financement, sa durée, etc. (Vous pouvez laisser ce champ vide)')
                    ->validationAttribute("Budget et dépenses"),
                TiptapEditor::make('admission_requirements')
                    ->label(false)
                    ->nullable()
                    ->output(TiptapOutput::Json)
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->maxContentWidth('full')
                    ->disableFloatingMenus()
                    ->placeholder("Informations sur les prérequis pour l'admission. (Vous pouvez laisser ce champ vide)")
                    ->validationAttribute("Critères d'admission"),
                TiptapEditor::make('apply_instructions')
                    ->nullable()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->maxContentWidth('full')
                    ->disableFloatingMenus()
                    ->label(false)
                    ->placeholder("Informations sur la marche à suivre pour candidater au projet/prix. (Vous pouvez laisser ce champ vide)")
                    ->validationAttribute('Pour postuler'),
                Fieldset::make('Internes')->schema([
                    Repeater::make('contact_ulb')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nom complet')
                                ->required()
                                ->minLength(3)
                                ->validationAttribute('Nom complet')
                                ->validationMessages([
                                    'required' => 'Le nom complet d\'un contact interne est obligatoire.',
                                    'min' => 'Le nom complet d\'un contact interne ne doit pas faire moins de :min caractères de long.',
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
                            TextInput::make('name')
                                ->label('Nom complet')
                                ->required()
                                ->minLength(3)
                                ->validationAttribute('Nom complet')
                                ->validationMessages([
                                    'required' => 'Le nom complet d\'un contact externe est obligatoire.',
                                    'min' => 'Le nom complet d\'un contact externe ne doit pas faire moins de :min caractères de long.',
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
                Select::make('poster_id')
                    ->relationship('poster', 'id')
                    ->label(false)
                    ->disabled()
                    ->default(fn () => Auth::id())
            ]);
    }

    public static function getFieldsetSchema(): array
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
                        ->minItems(1)
                        ->extraAttributes([
                            'class' => 'w-full'
                        ])
                        ->columns(3)
                        ->validationMessages([
                            'required' => "Le champ 'Disciplines scientifiques' est obligatoire.",
                            'min' => "Le champ 'Disciplines scientifiques' doit comprendre au moins :min élement.",
                        ])
                ])
                ->columnSpan(3)
                ->extraAttributes([
                    'class' => 'w-full disciplines-fieldset',
                ]);
        }

        return $fieldsets;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('title')->limit(50)->label('Titre')->searchable(),
                TextColumn::make('short_description')->limit(50)->label('Desc. courte'),
                TextColumn::make('poster.full_name')->limit(50)->label('Créateur'),
            ])
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        // Mettre à jour le statut du record
                        $record->update(['status' => -1]);

                        // Envoyer une notification Filament
                        Notification::make()
                            ->title('Projet archivé')
                            ->body("Le projet '{$record->title}' a été archivé avec succès.")
                            ->success() // Style de notification (success, danger, warning, etc.)
                            ->send();
                    })
                    ->label('Archiver'),

                Tables\Actions\ViewAction::make()
                    ->url(fn(Project $record) => route('projects.show', $record->id))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PosterRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
