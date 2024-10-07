<?php

namespace App\Livewire;

use App\Models\InfoSession;
use App\Models\ScientificDomainCategory;
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
use Livewire\Component;

class InfoSessionForm extends Component implements HasForms
{

    use InteractsWithForms;

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
                        ->required()
                        ->extraAttributes([
                            'class' => 'w-full'
                        ])->columns(3)
                ])
                ->columnSpan(3)
                ->extraAttributes([
                    'class' => 'w-full disciplines-fieldset',
                ]);
        }

        return $fieldsets;
    }

    public function submit(): void
    {
        try {
            $validatedData = $this->form->getState();

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

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make([
                TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->string()
                    ->columnSpanFull()
                ,
                RichEditor::make('description')
                    ->toolbarButtons(['underline', 'italic', 'bold'])
                    ->label('Description')
                    ->required()
                    ->string()
                    ->extraAttributes(['style' => 'max-height: 200px'])
                    ->columnSpanFull(),
                DateTimePicker::make('session_datetime')
                    ->seconds(false)
                    ->label('Date et heure')
                    ->columnSpan(1)
                    ->required(),
                TextInput::make('speaker')
                    ->label('Présentateur·ice')
                    ->string()
                    ->columnSpan(1),
                Select::make('session_type')
                    ->label('Type de session')
                    ->options([
                        2 => 'Hybride',
                        1 => 'Présentiel',
                        0 => 'Distanciel',
                    ])
                    ->required()
                    ->reactive(),
                TextInput::make('url')
                    ->required()
                    ->label('URL de la réunion')
                    ->url()
                    ->visible(fn($get) => in_array($get('session_type'), [2, 0]))
                    ->reactive(),
                TextInput::make('location')
                    ->required()
                    ->label('Adresse')
                    ->visible(fn($get) => in_array($get('session_type'), [2, 1])),
                Select::make('organisation_id')
                    ->required()
                    ->relationship('organisation', 'title')
                    ->label('Organisation')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('title')
                            ->required(),
                    ]),
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


