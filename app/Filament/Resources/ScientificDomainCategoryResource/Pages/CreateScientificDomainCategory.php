<?php

namespace App\Filament\Resources\ScientificDomainCategoryResource\Pages;

use App\Filament\Resources\ScientificDomainCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScientificDomainCategory extends CreateRecord
{
    protected static string $resource = ScientificDomainCategoryResource::class;
    protected static ?string $title = "Nouvelle catégorie de domaine scientifique";
}
