<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScientificDomainResource\Pages;
use App\Filament\Resources\ScientificDomainResource\RelationManagers;
use App\Models\ScientificDomain;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ScientificDomainResource extends Resource
{
    protected static ?string $model = ScientificDomain::class;
    protected static ?string $navigationGroup = 'Domaines Scientifiques';
    protected static ?string $label = 'Domaines';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Domaine scientifique')->required(),
                Select::make('sci_dom_cat_id')
                    ->relationship('category', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Catégorie')
                            ->required(),
                    ])
                    ->required()
                    ->label('Catégorie'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('category.name')->sortable(),
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
            'index' => Pages\ListScientificDomains::route('/'),
            'create' => Pages\CreateScientificDomain::route('/create'),
            'edit' => Pages\EditScientificDomain::route('/{record}/edit'),
        ];
    }
}
