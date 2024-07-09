<div>
    <div wire:ignore.self>
    @if (session('success'))
        <div class="mt-4 p-4 bg-green-500 text-white">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mt-4 p-4 bg-red-500 text-white">
            @foreach (session('error') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
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
