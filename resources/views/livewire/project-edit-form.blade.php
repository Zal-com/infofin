<div>
    <div wire:ignore.self>
    @if (session('success'))
        <div class="mt-4 p-4 bg-green-500 text-white">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 p-4 bg-red-500 text-white rounded-2xl mb-4">
            @foreach (session('error') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    <form wire:submit.prevent="submit" wire:model="project">
        {{ $this->form }}

        <div class="mt-4 flex justify-end gap-2">
            <x-filament::button type="button" wire:click="saveAsDraft">
                Garder en brouillon
            </x-filament::button>
            <x-filament::button type="submit">
                Ajouter le projet
            </x-filament::button>
        </div>

        <x-filament-actions::modals />
    </form>

</div>
