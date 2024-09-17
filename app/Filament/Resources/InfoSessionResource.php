<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfoSessionResource\Pages;
use App\Filament\Resources\InfoSessionResource\RelationManagers;
use App\Models\InfoSession;
use Filament\Forms;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                TextColumn::make('title')->label('Titre')->limit(30)->lineClamp(2)->width(30),
                TextColumn::make('organisation.title')->label('Organisation')->limit(30)->lineClamp(2),
                TextColumn::make('speaker')->label('Speaker.ine'),
                TextColumn::make('session_datetime')->label('Date & Heure')->dateTime('d/m/Y H:i'),
                TextColumn::make('session_type')->label('Type')->formatStateUsing(fn($state) => match ($state) {
                    0 => 'Distanciel',
                    1 => 'Présentiel',
                    2 => 'Hybride',
                })->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->label('Supprimer')
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
