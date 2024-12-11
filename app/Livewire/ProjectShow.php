<?php

namespace App\Livewire;

use App\Models\Collection;
use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use PHPUnit\Framework\MockObject\Exception;

class ProjectShow extends Component implements HasForms
{

    use InteractsWithForms;

    public Project $project;

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function addToFavorites()
    {
        try {
            Auth::user()->favorites()->attach($this->project->id);

            Notification::make()
                ->success()
                ->title('Ajouté aux favoris.')
                ->icon('heroicon-o-check-circle')
                ->send()
                ->seconds(5);
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title("Quelque chose ne s'est pas passé comme prévu. Veuillez réessayer.")
                ->icon('heroicon-o-x-circle')
                ->send()
                ->seconds(5);
        }
    }

    public function removeFromFavorites()
    {
        try {
            Auth::user()->favorites()->detach($this->project->id);

            Notification::make()
                ->success()
                ->title('Retiré des favoris.')
                ->icon('heroicon-o-check-circle')
                ->send()
                ->seconds(5);
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title("Quelque chose ne s'est pas passé comme prévu. Veuillez réessayer.")
                ->icon('heroicon-o-x-circle')
                ->send()
                ->seconds(5);
        }
    }

    public function archiveProject()
    {
        try {
            $this->project->update(['status' => -1]);
            $this->redirect(route('projects.index'));
            Notification::make()
                ->title('Projet archivé avec succès.')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->iconColor('success')
                ->seconds(5)
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title('Quelque chose ne s\'est pas passé comme prévu. Veuillez réessayer.')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->iconColor('danger')
                ->seconds(5)
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.project-show');
    }
}
