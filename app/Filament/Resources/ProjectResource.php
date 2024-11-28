<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use const http\Client\Curl\AUTH_ANY;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Repeater::make('contact_ulb')
                    ->schema([
                        TextInput::make('name')->string()->maxLength(255),
                        TextInput::make('email')->email(),
                    ]),
                Repeater::make('contact_ext')
                    ->schema([
                        TextInput::make('name')->string()->maxLength(255),
                        TextInput::make('email')->email(),
                    ]),
                Repeater::make('deadlines')
                    ->schema([
                        DatePicker::make('deadline'),
                        TextInput::make('proof')->string(),
                        Forms\Components\Toggle::make('continuous')
                    ]),
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
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Toggle::make('is_big')
                    ->required(),
                Forms\Components\Textarea::make('long_description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('short_description')
                    ->required()
                    ->maxLength(500),
                Forms\Components\TextInput::make('origin_url')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\Select::make('organisation_id')
                    ->relationship('organisation', 'title')
                    ->default(null),
                Forms\Components\TextInput::make('Pays')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('poster.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('visit_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_update_user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_big')
                    ->boolean(),
                Tables\Columns\TextColumn::make('short_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('origin_url')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_in_next_email')
                    ->boolean(),
                Tables\Columns\TextColumn::make('organisation.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Organisation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('OrganisationReference')
                    ->searchable(),
                Tables\Columns\IconColumn::make('InfoULB')
                    ->boolean(),
                Tables\Columns\IconColumn::make('SeanceFin')
                    ->boolean(),
                Tables\Columns\TextColumn::make('Pays')
                    ->searchable(),
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
