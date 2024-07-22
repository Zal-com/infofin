<?php

namespace App\Livewire;

use App\Models\InfoType;
use App\Models\ScientificDomain;
use App\Models\ScientificDomainCategory;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Livewire\Component;

class UserInterests extends Component implements HasForms
{
    use InteractsWithForms;

    public $data = [];

    public function render()
    {
        return view('livewire.user-interests');
    }

    public function mount()
    {
        $this->form->fill();
    }

    protected function getFieldsetSchema(): array
    {
        $categories = ScientificDomainCategory::with('domains')->get();
        $fieldsets = [];

        foreach ($categories as $category) {
            $sortedDomains = $category->domains->sortBy('name')->pluck('name', 'id')->toArray();
            $fieldsets[] = Fieldset::make($category->name)
                ->schema([
                    CheckboxList::make('appel.' . $category->id) // Unique name per category
                    ->options($sortedDomains)
                        ->label(false)
                        ->bulkToggleable()
                        ->columnSpan(2)
                        ->extraAttributes([
                            'class' => 'w-full'
                        ])->columns(3)// We already have the label in the fieldset title
                ])
                ->columnSpan(3)
                ->extraAttributes([
                    'class' => 'w-full'
                ]);
        }

        return $fieldsets;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make()
                ->tabs([
                    Tabs\Tab::make('Disciplines scientifiques')
                        ->schema($this->getFieldsetSchema()),
                    Tabs\Tab::make("Types d'appels")
                        ->schema([
                            CheckboxList::make('info_types')
                                ->label(false)
                                ->options(InfoType::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                ->columns(3)
                        ])
                ])->contained(false)
                ->extraAttributes(['class' => 'left-aligned-tabs'])
        ])->statePath('data')->model(User::class);
    }

}
