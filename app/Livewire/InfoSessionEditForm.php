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

class InfoSessionEditForm extends Component implements HasForms
{
    use InteractsWithForms;

    public InfoSession $info_session;
    public array $data = [];

    public function render()
    {
        return view('livewire.info-session-edit-form');
    }

    public function mount(InfoSession $info_session = null)
    {
        $this->info_session = $this->info_session ?? new InfoSession();
        $this->info_session->load('scientific_domains');

        // Transformer les domaines scientifiques en tableau d'ID
        $scientificDomainsIds = $this->info_session->scientific_domains->pluck('id')->toArray();

        // Pré-remplir le formulaire avec les données de la session et les domaines scientifiques sélectionnés
        $this->form->fill(array_merge(
            $this->info_session->toArray(),
            ['scientific_domains' => $scientificDomainsIds]
        ));
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

            $this->info_session->fill($validatedData);

            $this->info_session->update();

            $this->info_session->scientific_domains()->attach($validatedData['scientific_domains']);
            Notification::make()
                ->color('success')
                ->title('Session modifiée avec succès.')
                ->seconds(5)
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->send();
            $this->redirect(route('info_session.index'));
        } catch (\Exception $e) {
            Notification::make()
                ->title($e->getMessage())
                ->color('danger')
                ->seconds(5)
                ->icon('heroicon-o-x-circle')
                ->iconColor('danger')
                ->send();
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
                    ->reactive(),
                TextInput::make('url')
                    ->required()
                    ->label('URL de la réunion')
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
                    ->label('Modifier'),
            ])->alignEnd()
        ])->model($this->info_session)->statePath('data');

    }
}
