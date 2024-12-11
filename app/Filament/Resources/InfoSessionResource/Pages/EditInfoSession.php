<?php

namespace App\Filament\Resources\InfoSessionResource\Pages;

use App\Filament\Resources\InfoSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfoSession extends EditRecord
{
    protected static string $resource = InfoSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
