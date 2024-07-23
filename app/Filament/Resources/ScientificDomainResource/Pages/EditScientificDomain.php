<?php

namespace App\Filament\Resources\ScientificDomainResource\Pages;

use App\Filament\Resources\ScientificDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScientificDomain extends EditRecord
{
    protected static string $resource = ScientificDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
