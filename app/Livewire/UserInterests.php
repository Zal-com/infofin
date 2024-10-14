<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Expense;
use App\Models\User;
use App\Traits\ScientificDomainSchemaTrait;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class UserInterests extends Component implements HasForms
{
    use InteractsWithForms, ScientificDomainSchemaTrait;

    public User $user;
    public $data = [];

    public function render()
    {
        return view('livewire.user-interests');
    }

    public function mount()
    {
        $this->user = auth()->user();
        $this->user->load('activities', 'expenses', 'scientific_domains');

        $this->data = [
            'activities' => $this->user->activities->pluck('id')->toArray(),
            'expenses' => $this->user->expenses->pluck('id')->toArray(),
            'scientific_domains' => $this->user->scientific_domains->pluck('id')->toArray()
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make()
                ->tabs([
                    Tabs\Tab::make('Disciplines scientifiques')
                        ->schema($this->getFieldsetSchema()),
                    Tabs\Tab::make("Catégories d'activités")
                        ->schema([
                            CheckboxList::make('activities')
                                ->label('Catégories d\'activités')
                                ->options(Activity::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                ->columns(3)
                                ->relationship('activities', 'title')
                        ]),
                    Tabs\Tab::make("Catégories de dépenses éligibles")
                        ->schema([
                            CheckboxList::make('expenses')
                                ->label('Catégories de dépenses éligibles')
                                ->options(Expense::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                ->columns(3)
                                ->relationship('expenses', 'title')
                        ])
                ])->contained(false)
                ->extraAttributes(['class' => 'left-aligned-tabs'])
        ])->statePath('data')->model($this->user);
    }

    public function save()
    {
        try {
            $this->user->activities()->sync($this->data['activities']);
            $this->user->expenses()->sync($this->data['expenses']);
            $this->user->scientific_domains()->sync($this->data['scientific_domains']);
            Notification::make()
                ->title('Profil mis à jour.')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->seconds(5)
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Problème lors de la mise à jour du profil. Veuillez réessayer plus tard.')
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->seconds(5)
                ->send();
        }
    }
}
