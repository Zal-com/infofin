<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
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
                    ->maxLength(8)
                    ->minLength(8)
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
                    ->autocomplete(false)
                    ->nullable()
                    ->default(null),
                TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->label('Confirmation mot de passe')
                    ->autocomplete(false)
                    ->nullable()
                    ->default('null'),
            ])->statePath('data')->model($this->user)->columns(2);
    }

    public function submit()
    {

        if ($this->data['password'] === '') $this->data['password'] = null;

        $rules = [
            'email' => ['required', 'email', 'max:255'],
            'matricule' => ['required', 'max:8', 'min:8', 'regex:/^[2,5]\d{7}$/'],
            'first_name' => ['required', 'max:191'],
            'last_name' => ['required', 'max:191'],
            'password' => ['nullable', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
            'password_confirmation' => ['required_with:password', 'same:password'],
        ];

        $validator = Validator::make($this->data, $rules);

        if ($validator->fails()) {
            session()->flash('error', $validator->errors()->all());
            return redirect()->back()->withInput();
        }

        // Mise à jour des données utilisateur
        if ($this->data['password'] === null) {
            unset($this->data['password']);
        }
        $this->user->update($this->data);

        session()->flash('success', 'Profil mis à jour avec succès.');
        return redirect()->back()->withInput();
    }

    public function render()
    {
        return view('livewire.profile-form');
    }
}
