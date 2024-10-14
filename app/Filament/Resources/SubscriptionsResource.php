<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionsResource\Pages;
use App\Filament\Resources\SubscriptionsResource\RelationManagers;
use App\Models\Subscriptions;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionsResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $label = 'Abonnements';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('email')
                    ->searchable()
                    ->options(User::all()->pluck('email')->toArray())
                    ->required()
                    ->disabledOn('edit')
                    ->columnSpanFull(),
                CheckboxList::make('scientific_domain')
                    ->relationship('scientific_domains', 'name')
                    ->label('Domaines scientifiques')
                    ->columnSpanFull()
                    ->bulkToggleable()
                    ->columns(3),
                CheckboxList::make('activities')
                    ->relationship('activities', 'title')
                    ->label("Catégorie d'activités")
                    ->columnSpanFull()
                    ->bulkToggleable()
                    ->columns(2),
                CheckboxList::make('expenses')
                    ->relationship('expenses', 'title')
                    ->label("Catégorie de dépenses éligibles")
                    ->columnSpanFull()
                    ->bulkToggleable()
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::where('is_email_subscriber', '=', 1))
            ->columns([
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('activities')->label('Catégorie d\'activités')->badge(),
                TextColumn::make('expenses')->label('Catégorie de dépenses éligibles')->badge(),
                TextColumn::make('scientific_domain')->label('Domaines scientifiques')->badge(),
            ])
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->actionsPosition(Tables\Enums\ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscriptions::route('/create'),
            'edit' => Pages\EditSubscriptions::route('/{record}/edit'),
        ];
    }
}
