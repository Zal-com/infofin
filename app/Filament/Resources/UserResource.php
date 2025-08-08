<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $label = 'utilisateur';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $activeNavigationIcon = 'heroicon-s-users';
    protected static ?string $navigationGroup = 'Authentication';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('Prénom')
                    ->required(),
                TextInput::make('last_name')
                    ->label('Nom')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email', ignorable: fn($record) => $record)
                    ->rules('required|email'),
                Select::make('role')
                    ->label('Rôle')
                    ->relationship('roles', 'name')
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Intitulé du rôle'),
                        Select::make('permissions')
                            ->multiple()
                            ->options(fn() => cache()->remember('permissions_list', 86400, function () {
                                return Permission::all()->pluck('name', 'id');
                            }))
                    ]),
                TextInput::make('uid')
                    ->label('UID')
                    ->disabled(fn($record) => $record !== null) // Désactive si le record existe (édition)
                    ->required(fn($record) => $record === null), // Rend obligatoire uniquement à la création
                Checkbox::make('is_email_subscriber')
                    ->label('Abonnement à la newsletter'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select('users.*', 'roles.name as role_name')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('email')->searchable(isIndividual: true, isGlobal: true),
                TextColumn::make('first_name')->searchable(isIndividual: true, isGlobal: true),
                TextColumn::make('last_name')->searchable(isIndividual: true, isGlobal: true),
                TextColumn::make('role_name')->sortable(),
            ])
            ->paginationPageOptions([5, 10, 25, 50, 100])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
