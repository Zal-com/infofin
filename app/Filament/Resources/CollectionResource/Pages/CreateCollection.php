<?php

namespace App\Filament\Resources\CollectionResource\Pages;

use App\Filament\Resources\CollectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCollection extends CreateRecord
{
    protected static string $resource = CollectionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $data['id'] = \Illuminate\Support\Str::uuid();
        $data["user_id"] = auth()->id();
        dd($data);
        return static::getModel()::create($data);
    }
}
