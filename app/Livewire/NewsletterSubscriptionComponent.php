<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NewsletterSubscriptionComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public $data = [
        'is_email_subscriber' => false,
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->data['is_email_subscriber'] = $user->is_email_subscriber;
    }

    public function render()
    {
        return view('livewire.newsletter-subscription-component');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Toggle::make('is_email_subscriber')
                    ->label('Newsletter')
                    ->inlineLabel()
                    ->onColor('success')
                    ->onIcon('heroicon-o-check')
                    ->offColor('gray')
                    ->offIcon('heroicon-o-x-mark')
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->submit();
                    })
                    ->extraAttributes([
                        'class' => 'ml-auto whitespace-nowrap',
                    ])
            ])->extraAttributes([
                'class' => 'flex justify-between items-center mt-4'
            ])
        ])->statePath('data');
    }

    public function submit()
    {
        $user = Auth::user();

        try {
            $user->is_email_subscriber = $this->data['is_email_subscriber'];
            $user->save();

            Notification::make()
                ->title("La modification a bien été enregistrée.")
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->seconds(5)
                ->send();

        } catch (\Exception $exception) {
            Notification::make()
                ->title("Erreur lors de la modification, veuillez réessayer ou contacter l'administrateur du site.")
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->seconds(5)
                ->send();
        }
    }
}
