<?php

namespace App\Filament\Resources\ScientificDomainResource\Pages;

use App\Filament\Resources\ScientificDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScientificDomains extends ListRecords
{
    protected static string $resource = ScientificDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
