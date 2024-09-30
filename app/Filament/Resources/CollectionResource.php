<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Filament\Resources\CollectionResource\RelationManagers;
use App\Models\Collection;
use App\Models\Project;
use Faker\Core\Uuid;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Psy\Util\Str;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $activeNavigationIcon = 'heroicon-s-folder-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->string()
                    ->maxLength(255)
                    ->placeholder('Nom de la collection'),
                TextInput::make('description')
                    ->label('Description')
                    ->placeholder('Petit texte qui apparaîtra au dessus de votre collection.')
                    ->string()
                    ->maxLength(500),
                Select::make('projects')
                    ->label('Appels')
                    ->multiple()
                    ->searchable()
                    ->relationship('projects', 'title')
                    ->options(
                        Project::all()
                            ->mapWithKeys(function ($item) {
                                return [$item->id => $item->id . ' - ' . $item->title];
                            })),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('projects_count')
                    ->label('Appels')
                    ->sortable()
                    ->wrap()
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
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withCount('projects'))
            ->recordUrl(fn($record) => route('collection.show', $record->id));
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
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
