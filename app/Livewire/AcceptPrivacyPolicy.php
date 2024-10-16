<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Expense;
use App\Models\ScientificDomainCategory;
use App\Models\User;
use Auth;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class AcceptPrivacyPolicy extends Component implements HasForms
{
    use InteractsWithForms;

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

    protected function getFieldsetSchema(): array
    {
        $categories = ScientificDomainCategory::with('domains')->get();
        $fieldsets = [];

        foreach ($categories as $category) {
            $sortedDomains = $category->domains->sortBy('name')->pluck('name', 'id')->toArray();
            $fieldsets[] = Fieldset::make($category->name)
                ->schema([
                    CheckboxList::make('scientific_domains.' . $category->id)
                        ->options($sortedDomains)
                        ->label(false)
                        ->bulkToggleable()
                        ->columnSpan(2)
                        ->extraAttributes([
                            'class' => 'w-full'
                        ])->columns(2)
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
            Wizard::make([
                Step::make("Protection des données")
                    ->schema([
                        View::make('components.data-protection')
                            ->extraAttributes([
                                'class' => 'overflow-y-auto h-64',
                            ]),
                    ]),
                Step::make("Centres d'intérêts - Activités et Dépenses")
                    ->schema([
                        Section::make('Activités et Dépenses')
                            ->schema([
                                CheckboxList::make('activities')
                                    ->label("Catégorie d'activités")
                                    ->options(Activity::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                    ->columns(2)
                                    ->required(),
                                CheckboxList::make('expenses')
                                    ->label("Catégorie de dépenses éligibles")
                                    ->options(Expense::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                    ->columns(2)
                                    ->required(),
                            ])
                            ->columns(3),
                    ]),
                Step::make("Centres d'intérêts - Disciplines")
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
                Notification::make()
                    ->success()
                    ->title('Vous êtes abonné.e à la newsletter Infofin.')
                    ->body('Si vous ne voulez pas recevoir la newsletter, vous pouvez cliquer ici.')
                    ->actions([
                        Action::make('Me désabonner')
                            ->label('button')
                            ->action(fn() => \Illuminate\Support\Facades\Auth::user()->updateQuietly(['is_email_subscriber' => false]))
                    ]);
                Auth::login($user);

                return redirect()->route('projects.index');
            }
        } else {
            $oldUser->update($this->userDetails);

            if (isset($this->data['activities'])) {
                $oldUser->activities()->sync($this->data['activities']);
            }

            if (isset($this->data['expenses'])) {
                $oldUser->expenses()->sync($this->data['expenses']);
            }

            if (isset($this->data['scientific_domains'])) {
                $scientificDomains = collect($this->data['scientific_domains'])->flatten()->filter()->all();
                $oldUser->scientific_domains()->sync($scientificDomains);
            }
            Notification::make()
                ->success()
                ->title('Vous êtes abonné.e à la newsletter Infofin.')
                ->body('Si vous ne voulez pas recevoir la newsletter, vous pouvez cliquer ici.')
                ->actions([
                    Action::make('Me désabonner')
                        ->label('button')
                        ->action(fn() => \Illuminate\Support\Facades\Auth::user()->updateQuietly(['is_email_subscriber' => false]))
                ]);
            Auth::login($oldUser);

            return redirect()->route('projects.index');
        }
        return redirect()->route('login');
    }

}
