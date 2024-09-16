<?php

namespace App\Livewire;

use Auth;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\View;
use App\Models\InfoType;
use App\Models\ScientificDomainCategory;
use Filament\Forms\Components\Fieldset;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use App\Models\User;

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
                Step::make("Centres d'intérêt - Programmes")
                    ->schema([
                        Section::make("Types d'appels")
                            ->schema([
                                CheckboxList::make('info_types')
                                    ->label(false)
                                    ->bulkToggleable()
                                    ->options(InfoType::all()->sortBy('title')->pluck('title', 'id')->toArray())
                                    ->columns(2)
                                    ->validationAttribute("\"Types d'appels\"")
                                    ->requiredIf('is_email_subscriber', true)
                            ]),

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
                if (isset($this->data['info_types'])) {
                    $user->info_types()->sync($this->data['info_types']);
                }

                if (isset($this->data['scientific_domains'])) {
                    $scientificDomains = collect($this->data['scientific_domains'])->flatten()->filter()->all();
                    $user->scientific_domains()->sync($scientificDomains);
                }

                Auth::login($user);
                return redirect()->route('projects.index');
            }
        } else {
            $oldUser->update($this->userDetails);

            if (isset($this->data['info_types'])) {
                $oldUser->info_types()->sync($this->data['info_types']);
            }

            if (isset($this->data['scientific_domains'])) {
                $scientificDomains = collect($this->data['scientific_domains'])->flatten()->filter()->all();
                $oldUser->scientific_domains()->sync($scientificDomains);
            }

            Auth::login($oldUser);
            return redirect()->route('projects.index');
        }

        return redirect()->route('login');
    }

}
