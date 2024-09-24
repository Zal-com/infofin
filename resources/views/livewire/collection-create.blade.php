<div>
    <!-- Formulaire de sélection -->
    <form wire:submit.prevent="submit">
        <div>
            <!-- Nom du projet -->
            {{ $this->form }}
            {{ $this->table }}

            <x-filament::button type="submit">
                Enregistrer la sélection
            </x-filament::button>
        </div>
    </form>

    <!-- Tableau avec sélection multiple -->
</div>
