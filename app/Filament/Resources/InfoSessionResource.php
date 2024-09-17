<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfoSessionResource\Pages;
use App\Filament\Resources\InfoSessionResource\RelationManagers;
use App\Models\InfoSession;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Tiptap\Nodes\Text;

class InfoSessionResource extends Resource
{
    protected static ?string $model = InfoSession::class;
    protected static ?string $label = "Séances d'information";

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $activeNavigationIcon = 'heroicon-s-information-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->string()
                        ->columnSpanFull()
                    ,
                    RichEditor::make('description')
                        ->toolbarButtons(['underline', 'italic', 'bold'])
                        ->label('Description')
                        ->required()
                        ->string()
                        ->extraAttributes(['style' => 'max-height: 200px'])
                        ->columnSpanFull(),
                    DateTimePicker::make('session_datetime')
                        ->seconds(false)
                        ->label('Date et heure')
                        ->columnSpan(1)
                        ->required(),
                    TextInput::make('speaker')
                        ->label('Présentateur·ice')
                        ->string()
                        ->columnSpan(1),
                    Select::make('session_type')
                        ->label('Type de session')
                        ->options([
                            2 => 'Hybride',
                            1 => 'Présentiel',
                            0 => 'Distanciel',
                        ])
                        ->required()
                        ->reactive(),
                    TextInput::make('url')
                        ->required()
                        ->label('URL de la réunion')
                        ->url()
                        ->visible(fn($get) => in_array($get('session_type'), [2, 0]))
                        ->reactive(),
                    TextInput::make('location')
                        ->required()
                        ->label('Adresse')
                        ->visible(fn($get) => in_array($get('session_type'), [2, 1])),
                    Select::make('organisation_id')
                        ->required()
                        ->relationship('organisation', 'title')
                        ->label('Organisation')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('title')
                                ->required(),
                        ]),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->limit(30)
                    ->lineClamp(2)
                    ->width(30)
                    ->searchable(isIndividual: true),
                TextColumn::make('organisation.title')
                    ->label('Organisation')
                    ->limit(30)
                    ->lineClamp(2)
                    ->searchable(isIndividual: true),
                TextColumn::make('speaker')
                    ->label('Speaker.ine')
                    ->searchable(isIndividual: true),
                TextColumn::make('session_datetime')
                    ->label('Date & Heure')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('session_type')
                    ->label('Type')
                    ->formatStateUsing(fn($state) => match ($state) {
                        0 => 'Distanciel',
                        1 => 'Présentiel',
                        2 => 'Hybride',
                    })->sortable()
            ])->paginationPageOptions([5, 10, 25, 50, 100])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\Action::make('show')
                ->label('Voir')
                ->icon('heroicon-o-eye')
                ->action(fn($record) => redirect(route('info_session.show', $record->id)))
                ->color('secondary'),
                Tables\Actions\EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->label('Supprimer'),
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
            'index' => Pages\ListInfoSessions::route('/'),
            'create' => Pages\CreateInfoSession::route('/create'),
            'edit' => Pages\EditInfoSession::route('/{record}/edit'),
        ];
    }
}
