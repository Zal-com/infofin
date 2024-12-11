<?php

namespace App\Livewire;

use App\Models\InfoSession;
use Filament\Notifications\Notification;
use Livewire\Component;

class InfoSessionShow extends Component
{
    public InfoSession $info_session;

    public function render()
    {
        return view('livewire.info-session-show');
    }

    public function mount(InfoSession $infoSession)
    {
        $this->info_session = $infoSession;
    }

    public function delete()
    {
        try {
            $this->info_session->update(['status' => 0]);

            Notification::make('success')
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->title("La session a été supprimée avec succès.")
                ->seconds(5)
                ->send();

            $this->redirect(route('info_session.index'));
        } catch (\Exception $e) {
            Notification::make('warning')
                ->icon('heroicon-o-exclamation-circle')
                ->iconColor('warning')
                ->title("Quelque chose ne s'est pas passé comme prévu. Veuillez réessayer.")
                ->seconds(5)
                ->send();
        }
    }
}
