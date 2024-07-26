<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
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
                Checkbox::make('is_internal')
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        if (!$state) {
                            $set('matricule', '99999999');
                        } else {
                            $set('matricule', '');
                        }
                    })
                    ->reactive()
                    ->default(true)
                    ->label('Interne ULB'),
                TextInput::make('matricule')
                    ->required()
                    ->disabled(fn(Get $get): bool => !$get('is_internal')),
                TextInput::make('email')->email()->unique('users')->required(),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->required()
                    ->disabled()
                    ->password()
                    ->default(Str::password(8)),
                Checkbox::make('is_email_subscriber')
                    ->label('Abonnement à la newsletter')
                    ->default(false)
                    ->disabled(),
                Select::make('role')
                    ->label('Rôle')
                    ->relationship('roles', 'name')
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Intitulé du rôle'),
                        Select::make('permissions')
                            ->multiple()
                            ->options(Permission::all()->pluck('name', 'id'))
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('email'),
                TextColumn::make('first_name'),
                TextColumn::make('last_name'),
                TextColumn::make('role'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
