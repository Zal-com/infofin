<div>
    <form wire:submit.prevent="submit" wire:model="project">
        {{ $this->form }}

        <div class="mt-4">
            <button type="submit" class="btn primary">
                Ajouter le projet
            </button>
        </div>

        <x-filament-actions::modals />
    </form>
</div>
