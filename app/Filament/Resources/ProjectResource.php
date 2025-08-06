<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Project;
use App\Models\ScientificDomainCategory;
use App\Traits\ScientificDomainSchemaTrait;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use const http\Client\Curl\AUTH_ANY;

class ProjectResource extends Resource
{
    use ScientificDomainSchemaTrait;

    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('short_description')
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),
                TiptapEditor::make('long_description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(2)
                    ->default(1)
                    ->columnSpan(1),
                Forms\Components\Toggle::make('is_big')
                    ->required()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('origin_url')
                    ->maxLength(191)
                    ->default(null)
                    ->columnSpan(1),
                Fieldset::make('contacts')
                    ->schema([
                        Forms\Components\Repeater::make('contact_ulb')
                            ->schema([
                                TextInput::make('name')->string()->maxLength(255),
                                TextInput::make('email')->email(),
                            ]),
                        Repeater::make('contact_ext')
                            ->schema([
                                TextInput::make('name')->string()->maxLength(255),
                                TextInput::make('email')->email(),
                            ])
                    ]),
                Repeater::make('deadlines')
                    ->schema([
                        DatePicker::make('date'),
                        TextInput::make('proof')->string(),
                        Forms\Components\Toggle::make('continuous')
                    ])->columnSpan(2),
                TiptapEditor::make('admission_requirements')
                    ->columnSpanFull(),
                TiptapEditor::make('funding')
                    ->columnSpanFull(),
                TiptapEditor::make('apply_instructions')
                    ->columnSpanFull(),
                Forms\Components\Select::make('poster_id')
                    ->relationship('poster', 'id')
                    ->required()
                    ->default(fn() => Auth::id()),
                Forms\Components\TextInput::make('last_update_user_id')
                    ->required()
                    ->numeric()
                    ->default(Auth::id()),
                Forms\Components\Select::make('organisation_id')
                    ->relationship('organisation', 'title')
                    ->default(null)
                    ->columnSpan(2),
                Forms\Components\Fieldset::make('scientific_domains')
                    ->label('Domaines scientifiques')
                    ->schema(function (): array {
                        $categories = cache()->remember('scientific_domain_categories', 86400, function () {
                            return ScientificDomainCategory::with('domains')->get();
                        });
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
                    }),
            ])
            ->columns(3);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['organisation', 'poster']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(300)
                    ->wrap()
                    ->lineClamp(2),
                Tables\Columns\IconColumn::make('is_big')
                    ->label('P. majeur')
                    ->alignCenter()
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('short_description')
                    ->searchable()
                    ->limit(300)
                    ->wrap()
                    ->lineClamp(2)
                    ->html(),
                Tables\Columns\TextColumn::make('organisation.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
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
