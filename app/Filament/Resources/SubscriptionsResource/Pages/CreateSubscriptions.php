<?php

namespace App\Filament\Resources\SubscriptionsResource\Pages;

use App\Filament\Resources\SubscriptionsResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriptions extends CreateRecord
{
    protected static string $resource = SubscriptionsResource::class;
    protected static ?string $title = "Nouvelle souscription à la newsletter";

    protected array $scientific_domains = [];
    protected array $info_types = [];

    protected function beforeValidate()
    {
        $this->scientific_domains = $this->data['scientific_domains'] ?? [];
        $this->info_types = $this->data['info_types'] ?? [];
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $user = User::where("id", $data['email'] + 1)->first();

        if (!empty($this->info_types)) {
            $user->info_types()->sync($this->info_types);
        }

        if (isset($this->scientific_domains)) {
            $user->scientific_domains()->sync($this->scientific_domains);
        }

        $user->is_email_subscriber = 1;
        $user->save();

        return $user;
    }
}
