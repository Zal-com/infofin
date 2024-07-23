<?php

namespace App\Filament\Resources\ScientificDomainCategoryResource\Pages;

use App\Filament\Resources\ScientificDomainCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScientificDomainCategory extends EditRecord
{
    protected static string $resource = ScientificDomainCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
