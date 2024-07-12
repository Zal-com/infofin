<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class ProfileForm extends Component implements HasForms
{
    use InteractsWithForms;

    public User $user;
    public array $data = [];

    public function mount(User $user = null)
    {
        $this->user = $user ?? new User();
        $this->form->fill($this->user->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->required(),
                Section::make('Nom')->schema(
                    [
                        TextInput::make('first_name')
                            ->required(),
                        TextInput::make('last_name')
                            ->required()
                    ]
                )
            ])->statePath('data')->model($this->user);
    }

    public function render()
    {
        return view('livewire.profile-form');
    }
}
