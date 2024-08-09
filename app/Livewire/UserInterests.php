<?php

namespace App\Livewire;

use App\Models\InfoType;
use App\Models\ScientificDomainCategory;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class UserInterests extends Component implements HasForms
{
    use InteractsWithForms;

    public User $user;
    public $data = [];

    public function render()
    {
        return view('livewire.user-interests');
    }

    public function mount()
    {
        $this->user = auth()->user();
        $this->user->load('info_types', 'scientific_domains');

        $this->data = [
            'info_types' => $this->user->info_types->pluck('id')->toArray(),
            'scientific_domains' => $this->user->scientific_domains->pluck('id')->toArray()
        ];

        $this->form->fill($this->data);
    }

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
                        ->extraAttributes([
                            'class' => 'w-full'
                        ])->columns(3)
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
                                ->label('Types de programmes')
                                ->options(InfoType::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                ->columns(3)
                                ->relationship('info_types', 'title')
                        ])
                ])->contained(false)
                ->extraAttributes(['class' => 'left-aligned-tabs'])
        ])->statePath('data')->model($this->user);
    }

    public function save()
    {
        try {
            $this->user->info_types()->sync($this->data['info_types']);
            $this->user->scientific_domains()->sync($this->data['scientific_domains']);
            Notification::make()
                ->title('Profil mis à jour.')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->seconds(5)
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Problème lors de la mise a jour du profil. Veuillez réessayer plus tard.')
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->seconds(5)
                ->send();
        }
    }
}
