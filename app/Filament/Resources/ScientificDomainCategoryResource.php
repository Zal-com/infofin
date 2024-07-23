<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScientificDomainCategoryResource\Pages;
use App\Filament\Resources\ScientificDomainCategoryResource\RelationManagers;
use App\Models\ScientificDomainCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScientificDomainCategoryResource extends Resource
{
    protected static ?string $model = ScientificDomainCategory::class;

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
                TextColumn::make('id'),
                TextColumn::make('name'),
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
            'index' => Pages\ListScientificDomainCategories::route('/'),
            'create' => Pages\CreateScientificDomainCategory::route('/create'),
            'edit' => Pages\EditScientificDomainCategory::route('/{record}/edit'),
        ];
    }
}
