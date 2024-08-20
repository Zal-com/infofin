<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class MailingPage extends Page
{

    protected static ?string $navigationLabel = 'Mailing';
    protected static ?string $navigationGroup = 'Communication';
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static string $view = 'filament.pages.mailing-page';

    public function getHeading(): string
    {
        return 'Mailing Management';
    }

    protected function getViewData(): array
    {
        return [
            'livewireComponent' => 'mailing-projects-table',
        ];
    }
}
