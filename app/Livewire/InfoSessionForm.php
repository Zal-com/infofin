<?php

namespace App\Livewire;

use App\Models\InfoSession;
use App\Models\Organisation;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
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

    public function submit(): void
    {
        try {
            $validatedData = $this->form->getState();

            $this->infoSession->fill($validatedData);

            $this->infoSession->save();
            Notification::make()
                ->color('success')
                ->seconds(5)
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title("Erreur lors de l'ajout d'une session d'information")
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
                        'Hybride' => 'Hybride',
                        'Présentiel uniquement' => 'Présentiel uniquement',
                        'Distanciel uniquement' => 'Distanciel uniquement',
                    ])
                    ->reactive(),
                TextInput::make('url')
                    ->required()
                    ->label('URL de la réunion')
                    ->visible(fn($get) => in_array($get('session_type'), ['Hybride', 'Distanciel uniquement']))
                    ->reactive(),
                TextInput::make('location')
                    ->required()
                    ->label('Adresse')
                    ->visible(fn($get) => in_array($get('session_type'), ['Hybride', 'Présentiel uniquement'])),
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


