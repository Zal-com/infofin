<?php

namespace App\Livewire;

use App\Models\InfoSession;
use App\Models\ScientificDomainCategory;
use App\Traits\ScientificDomainSchemaTrait;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class InfoSessionForm extends Component implements HasForms
{

    use InteractsWithForms;
    use ScientificDomainSchemaTrait;

    public $data = [];
    public InfoSession $infoSession;

    public function render()
    {
        return view('livewire.info-session-form');
    }

    public function mount(InfoSession $infoSession): void
    {
        $this->infoSession = $this->infoSession ?? new InfoSession();
        $this->form->fill(
            $this->infoSession->toArray()
        );
    }

    protected function onValidationError(ValidationException $exception): void
    {
        foreach ($exception->errors() as $error) {
            foreach ($error as $e) {
                Notification::make()
                    ->title($e)
                    ->warning()
                    ->icon('heroicon-o-exclamation-circle')
                    ->color('warning')
                    ->iconColor('warning')
                    ->seconds(5)
                    ->send();
            }

        }

    }


    public function submit(): void
    {
        if ($this->form->validate()) {

            try {
                $validatedData = $this->data;

                $this->infoSession->fill($validatedData);

                $this->infoSession->save();

                $this->infoSession->scientific_domains()->attach($validatedData['scientific_domains']);
                Notification::make()
                    ->color('success')
                    ->seconds(5)
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('success')
                    ->title('Session créée avec succès.')
                    ->send();
                $this->redirect(route('info_session.index'));
            } catch (\Illuminate\Validation\ValidationException $e) {
                foreach ($e->errors() as $error) {
                    Notification::make()
                        ->title($error[0])
                        ->color('danger')
                        ->seconds(5)
                        ->icon('heroicon-o-x-circle')
                        ->iconColor('danger')
                        ->send();
                }
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make([
                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->string()
                    ->validationAttribute("Titre")
                    ->validationMessages([
                        'required' => 'Le champ ":attribute" est obligatoire.',
                        'string' => 'Le champ ":attribute" doit être une chaîne de caractères.',
                    ])
                    ->columnSpanFull()
                ,
                RichEditor::make('description')
                    ->toolbarButtons(['underline', 'italic', 'bold'])
                    ->label('Description')
                    ->required()
                    ->string()
                    ->extraAttributes(['style' => 'max-height: 200px'])
                    ->validationAttribute('Description')
                    ->validationMessages([
                        'required' => 'Le champ ":attribute" est obligatoire.',
                        'string' => 'Le champ ":attribute" doit être une chaîne de caractères.',
                    ])
                    ->columnSpanFull(),
                \LaraZeus\Accordion\Forms\Accordions::make('Disciplines scientifiques')
                    ->columnSpan(2)
                    ->activeAccordion(2)
                    ->isolated()
                    ->accordions([
                        \LaraZeus\Accordion\Forms\Accordion::make('main-data')
                            ->columns()
                            ->label('Disciplines scientifiques')
                            ->schema($this->getFieldsetSchema()),
                    ]),
                DateTimePicker::make('session_datetime')
                    ->seconds(false)
                    ->label('Date et heure')
                    ->after('today')
                    ->columnSpan(1)
                    ->required()
                    ->validationAttribute('Date et heure')
                    ->validationMessages([
                        'required' => 'Le champ ":attribute" est obligatoire.',
                        'after' => 'Le champ ":attribute" doit avoir une valeur ultérieure à la date du jour.',
                    ]),
                TextInput::make('speaker')
                    ->label('Présentateur·ice')
                    ->string()
                    ->nullable()
                    ->columnSpan(1)
                    ->validationAttribute('Présentateur·ice')
                    ->validationMessages([
                        'string' => 'Le champ ":attribute" doit être une chaîne de caractères.',
                    ]),
                Select::make('session_type')
                    ->label('Type de session')
                    ->options([
                        2 => 'Hybride',
                        1 => 'Présentiel',
                        0 => 'Distanciel',
                    ])
                    ->default(2)
                    ->required()
                    ->reactive()
                    ->validationAttribute('Type de session')
                    ->validationMessages([
                        'required' => 'Le champ ":attribute" est obligatoire.',
                    ]),
                TextInput::make('url')
                    ->required()
                    ->label('URL de la réunion')
                    ->url()
                    ->activeUrl()
                    ->visible(fn($get) => in_array($get('session_type'), [2, 0]))
                    ->reactive()
                    ->validationAttribute('URL de la réunion')
                    ->validationMessages([
                        'required' => 'Le champ ":attribute" est obligatoire.',
                        'activeUrl' => 'L\'URL renseignée n\'est pas jugée sûre.',
                        'url' => 'Le champ ":attribute" doit être un URL valide.',
                    ]),
                TextInput::make('location')
                    ->required()
                    ->string()
                    ->label('Adresse')
                    ->visible(fn($get) => in_array($get('session_type'), [2, 1]))
                    ->validationAttribute('Adresse')
                    ->validationMessages([
                        'required' => 'Le champ ":attribute" est obligatoire.',
                        'string' => 'Le champ ":attribute" doit être une chaîne de caractères.',
                    ]),
                Select::make('organisation_id')
                    ->required()
                    ->relationship('organisation', 'title')
                    ->label('Organisation')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('title')
                            ->required(),
                    ])
                    ->validationAttribute('Organisation')
                    ->validationMessages([
                        'required' => 'Le champ ":attribute" est obligatoire.',
                    ]),
            ])->columns(2),
            Actions::make([
                Action::make('submit')
                    ->action('submit')
                    ->color('primary')
                    ->icon('heroicon-o-check')
                    ->label('Ajouter'),
            ])->alignEnd()
        ])->model($this->infoSession)->statePath('data');

    }
}


