<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileForm extends Component implements HasForms
{
    use InteractsWithForms;

    public User $user;
    public array $data = [];

    public function mount(User $user = null)
    {
        $this->user = Auth::user() ?? new User();
        $this->form->fill($this->user->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(191),
                TextInput::make('matricule')
                    ->label('Matricule')
                    ->required()
                    ->columnSpan(2),
                TextInput::make('first_name')
                    ->label('Prénom')
                    ->required()
                    ->maxLength(191),
                TextInput::make('last_name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(191),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->label('Mot de passe')
                    ->autocomplete(false),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->label('Confirmation mot de passe')
                    ->autocomplete(false),
            ])->statePath('data')->model($this->user)->columns(2);
    }

    public function submit(): void
    {
        $this->validate();
    }

    public function render()
    {
        return view('livewire.profile-form');
    }
}
