<?php

namespace App\Filament\Resources\InfoSessionResource\Pages;

use App\Filament\Resources\InfoSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInfoSessions extends ListRecords
{
    protected static string $resource = InfoSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
