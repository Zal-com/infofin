<?php

namespace App\Filament\Resources\InfoSessionResource\Pages;

use App\Filament\Resources\InfoSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInfoSession extends CreateRecord
{
    protected static string $resource = InfoSessionResource::class;
    protected static ?string $title = 'Nouvelle séance d\'information';
}
