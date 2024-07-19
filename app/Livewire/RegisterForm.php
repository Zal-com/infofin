<?php

namespace App\Livewire;

use App\Models\InfoType;
use App\Models\ScientificDomain;
use App\Models\ScientificDomainCategory;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class RegisterForm extends Component implements HasForms
{
    use InteractsWithForms;

    public array $data = [];

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
                        ->columnSpan(2)
                        ->extraAttributes([
                            'class' => 'w-full'
                        ])->columns(2)// We already have the label in the fieldset title
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
                Step::make('Informations personnelles')
                    ->schema([
                        TextInput::make('last_name')
                            ->label('Nom')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('first_name')
                            ->label('Prénom')
                            ->required()
                            ->columnSpan(1),
                        Grid::make()->schema([
                            TextInput::make('email')
                                ->label('Email')
                                ->required()
                                ->columnSpan(1),
                        ]),
                        Grid::make()->schema([
                            Checkbox::make('is_mail_subscriber')
                                ->label("J'accepte de recevoir des mails en lien avec mes centres d'intérêts")
                                ->default(true)
                                ->columnSpan(1),
                        ])->columns(2),
                        TextInput::make('password')
                            ->password()
                            ->label('Mot de passe')
                            ->required()
                            ->revealable(),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->label('Confirmer le mot de passe')
                            ->required()
                            ->revealable(),
                        Grid::make()->schema([
                            Checkbox::make('is_internal')
                                ->label("Je suis membre interne ou étudiant à l'ULB")
                                ->default(false)
                                ->live()
                                ->columnSpan(1),
                        ])->columns(2),

                        TextInput::make('matricule')
                            ->label('Matricule')
                            ->required()
                            ->hidden(fn(Get $get): bool => !$get('is_internal') == true)
                            ->live()
                            ->placeholder('02XXXXXX / 05XXXXXX')
                            ->columnSpan(1),
                    ])->columns(2),
                Step::make("Centres d'intérêt - Programmes")
                    ->schema([
                        Section::make("Types d'appels")
                            ->schema([
                                CheckboxList::make('info_types')
                                    ->label(false)
                                    ->options(InfoType::all()->sortBy('title')->pluck('title')->toArray())
                                    ->columns(2)
                            ]),

                    ]),
                Step::make("Centres d'intérêts - Disciplines")
                    ->schema([
                        Section::make('Disciplines scientifiques')
                            ->schema($this->getFieldsetSchema())
                            ->columns(3),
                    ])
            ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button type="submit"><i class="fa fa-solid fa-plus mr-2"></i>Créer mon compte</x-filament::button>
                BLADE
            ))),
        ])->statePath('data');
    }
}
