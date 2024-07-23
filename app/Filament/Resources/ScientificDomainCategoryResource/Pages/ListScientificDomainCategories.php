<?php

namespace App\Filament\Resources\ScientificDomainCategoryResource\Pages;

use App\Filament\Resources\ScientificDomainCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScientificDomainCategories extends ListRecords
{
    protected static string $resource = ScientificDomainCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
