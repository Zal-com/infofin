<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Continent;
use App\Models\Countries;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
                    ->maxLength(255)
                    ->required(),
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
                Select::make('info')
                    ->label("Type d'information")
                    ->options([
                        'Financement',
                        "Séance d'information organisée par l'ULB",
                        "Séance d'information organisée par un organisme externe"
                    ])
                    ->selectablePlaceholder(false)
                    ->required(),
                CheckboxList::make('info_types')
                    ->label('Types de programmes')
                    ->options(InfoType::all()->sortBy('title')->pluck('title')->toArray())
                    ->columns(3)
                    ->columnSpanFull()
                    ->required(),
                Select::make('scientific_domains')
                    ->label("Disciplines scientifiques de l'appel")
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
                Textarea::make('short_description')
                    ->label('Description courte')
                    ->maxLength(500)
                    ->hint(fn($state, $component) => strlen($state) . '/' . $component->getMaxLength())
                    ->live()
                    ->columnSpanFull()
                    ->required(),
                MarkdownEditor::make('long_description')
                    ->label('Description complète')
                    ->columnSpanFull()
                    ->required(),
                MarkdownEditor::make('funding')
                    ->hint("Informations sur le financement et le budget de l'appel")
                    ->label("Financement")
                    ->columnSpanFull()
                    ->required(),
                MarkdownEditor::make('admission_requirements')
                    ->label("Critères d'admission")
                    ->columnSpanFull()
                    ->required(),
                MarkdownEditor::make('apply_instructions')
                    ->label("Pour postuler")
                    ->columnSpanFull()
                    ->required(),
                Repeater::make('contact_ulb')->schema([
                    TextInput::make('first_name')->label('Prénom'),
                    TextInput::make('last_name')->label('Nom'),
                    TextInput::make('email')->label('E-mail')->email(),
                ])->columns(2)->addActionLabel('+ Nouveau contact')->label('Contact ULB'),
                Repeater::make('contact_ext')->schema([
                    TextInput::make('first_name')->label('Prénom'),
                    TextInput::make('last_name')->label('Nom'),
                    TextInput::make('email')->label('E-mail')->email(),
                ])->columns(2)->addActionLabel('+ Nouveau contact')->label('Contact EXTERNE'),
                FileUpload::make('docs')
                    ->label('Documents')
                    ->multiple()
                    ->preserveFilenames()
                    ->disk('public')
                    ->directory('media')
                    ->acceptedFileTypes(['pdf'])
                    ->maxFiles(10),
                TextInput::make('poster_id')->label(false)->hidden()->default(Auth::id()),
            ]);
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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
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
