<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = "Modifier l'utilisateur";

    protected function getHeaderActions(): array
    {
        if ($this->record->id === 1) {
            return [];
        }

        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    $user = User::find($this->record->id);
                    if ($user) {
                        $user->reassignAndDelete(1);
                    }
                }),
        ];
    }
}
