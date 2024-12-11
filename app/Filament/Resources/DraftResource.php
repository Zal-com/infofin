<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DraftResource\Pages;
use App\Filament\Resources\DraftResource\RelationManagers;
use App\Models\Draft;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DraftResource extends Resource
{
    protected static ?string $model = Draft::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static ?string $activeNavigationIcon = 'heroicon-s-pencil';

    protected static ?string $label = "Brouillons";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id"),
                Tables\Columns\TextColumn::make('content.title')
                    ->label("Titre"),
                TextColumn::make('content.short_description')
                    ->label('Desc. courte')
                    ->limit(50)
            ])
            ->recordUrl(null)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Modifier')
                    ->icon('heroicon-o-pencil')
                    ->action(fn($record) => redirect(route('projects.create', ['record' => $record->id]))
                    ),
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
            'index' => Pages\ListDrafts::route('/'),
            'create' => Pages\CreateDraft::route('/create'),
            'edit' => Pages\EditDraft::route('/{record}/edit'),
        ];
    }
}
