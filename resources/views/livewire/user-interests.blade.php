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
    <form wire:submit.prevent="save">
        {{$this->form}}
        <div class="flex justify-end mt-5">
            <x-filament::button type="submit" icon="heroicon-o-pencil-square">
                Sauvegarder
            </x-filament::button>
        </div>
    </form>
</div>
