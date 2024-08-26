<?php

namespace App\Livewire;

use App\Models\NewsletterSchedule;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class BeginMail extends Component implements HasForms
{
    use InteractsWithForms;

    public NewsletterSchedule $newsletterSchedule;
    public array $data = [];

    public function mount(NewsletterSchedule $newsletterSchedule = null)
    {
        $this->newsletterSchedule = NewsletterSchedule::first() ?? new NewsletterSchedule();
        $this->form->fill($this->newsletterSchedule->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            RichEditor::make('message')
                ->label('Message informatif')
                ->toolbarButtons(['bold', 'underline', 'italic'])
                ->maxLength(255)
                ->string()
                ->required(),
            Actions::make([
                FormAction::make('submit')
                    ->label('Enregistrer')
                    ->action('submit')
            ])->alignEnd()
        ])->statePath("data")->model($this->newsletterSchedule);
    }

    public function submit()
    {
        $message = $this->data['message'];
        $this->newsletterSchedule->message = $message;
        $this->newsletterSchedule->save();
        Notification::make()
            ->title('Message changé avec succès.')
            ->color('success')
            ->seconds(5)
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->send();
    }


    public function render()
    {
        return view('livewire.begin-mail');
    }
}
