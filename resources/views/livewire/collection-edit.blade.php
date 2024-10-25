<div>
    <x-filament::button
        icon="heroicon-o-arrow-uturn-left"
        tag="a"
        href="{{route('profile.show')}}"
    >
        Retour
    </x-filament::button>
    <h2 class="my-4">Modifier la collection</h2>
    <div class="flex flex-col gap-6">
        {{$this->form}}
        {{$this->table}}
    </div>
</div>
