<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Expense;
use App\Models\User;
use App\Traits\ScientificDomainSchemaTrait;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class AcceptPrivacyPolicy extends Component implements HasForms
{
    use InteractsWithForms, ScientificDomainSchemaTrait;

    public $userDetails;
    public array $data = [];

    public function mount()
    {
        $this->form->fill();
    }


    public function render()
    {
        return view('livewire.accept-privacy-policy');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Wizard::make([
                Step::make("Protection des données")
                    ->schema([
                        View::make('components.data-protection')
                            ->extraAttributes([
                                'class' => 'overflow-y-auto h-64',
                            ]),
                    ]),
                Step::make("Centres d'intérêt - Activités et Dépenses")
                    ->schema([
                        Section::make('Activités et Dépenses')
                            ->schema([
                                CheckboxList::make('activities')
                                    ->label(new HtmlString("<strong>Catégorie d'activités</strong>"))
                                    ->options(Activity::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                    ->columns(2)
                                    ->bulkToggleable()
                                    ->required(),
                                CheckboxList::make('expenses')
                                    ->label(new HtmlString("<strong>Catégorie de dépenses éligibles</strong>"))
                                    ->bulkToggleable()
                                    ->options(Expense::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                    ->columns(2)
                                    ->required(),
                            ])
                            ->columns(2),
                    ]),
                Step::make("Centres d'intérêt - Disciplines")
                    ->schema([
                        Section::make('Disciplines scientifiques')
                            ->schema($this->getFieldsetSchema())
                            ->columns(3),
                    ])
            ])->submitAction(new HtmlString(Blade::render(<<<BLADE
            <x-filament::button type="submit"><i class="fa fa-solid fa-plus mr-2"></i>Me connecter</x-filament::button>
            BLADE
            ))),
        ])->statePath('data');
    }

    public function submit()
    {
        $oldUser = User::where("email", "=", $this->userDetails['email'])->first();

        if (!$oldUser) {
            if ($user = User::create($this->userDetails)) {
                $this->syncUserData($user);
                Auth::login($user);
            }
        } else {
            $oldUser->update($this->userDetails);
            $this->syncUserData($oldUser);
            Auth::login($oldUser);
        }

        $this->showNotification();

        return redirect()->route('projects.index');
    }

    private function syncUserData($user)
    {
        if (isset($this->data['activities'])) {
            $user->activities()->sync($this->data['activities']);
        }

        if (isset($this->data['expenses'])) {
            $user->expenses()->sync($this->data['expenses']);
        }

        if (isset($this->data['scientific_domains'])) {
            $scientificDomains = collect($this->data['scientific_domains'])->flatten()->filter()->all();
            $user->scientific_domains()->sync($scientificDomains);
        }
    }

    private function showNotification()
    {
        Notification::make()
            ->success()
            ->title('Vous êtes abonné.e à la newsletter Infofin.')
            ->body('Si vous ne voulez pas recevoir la newsletter, vous pouvez cliquer ici. Cette action est réversible.')
            ->actions([
                \Filament\Notifications\Actions\Action::make('Me désabonner')
                    ->button()
                    ->close()
                    ->action(function () {
                        Auth::user()->update(['is_email_subscriber' => false]);
                        Notification::make()
                            ->success()
                            ->title('Vous êtes désabonné.e de la newsletter Infofin.')
                            ->send();
                    }),
            ])
            ->send();
    }

}
