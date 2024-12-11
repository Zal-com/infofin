<?php

namespace App\Traits;

use App\Models\ScientificDomainCategory;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;

trait ScientificDomainSchemaTrait
{
    protected function getFieldsetSchema(): array
    {
        $categories = ScientificDomainCategory::with('domains')->get();
        $fieldsets = [];

        foreach ($categories as $category) {
            $sortedDomains = $category->domains->sortBy('name')->pluck('name', 'id')->toArray();
            $fieldsets[] = Fieldset::make($category->name)
                ->schema([
                    CheckboxList::make('scientific_domains')
                        ->label(false)
                        ->options($sortedDomains)
                        ->bulkToggleable()
                        ->columnSpan(2)
                        ->required()
                        ->minItems(1)
                        ->extraAttributes([
                            'class' => 'w-full'
                        ])
                        ->columns(3)
                        ->validationMessages([
                            'required' => "Le champ 'Disciplines scientifiques' est obligatoire.",
                            'min' => "Le champ 'Disciplines scientifiques' doit comprendre au moins :min élement.",
                        ])
                ])
                ->columnSpan(3)
                ->extraAttributes([
                    'class' => 'w-full disciplines-fieldset',
                ]);
        }

        return $fieldsets;
    }
}
