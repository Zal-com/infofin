<div>
    <x-filament::icon-button tag="a" icon="heroicon-o-arrow-uturn-left"
                             class="my-4"
                             href="{{\Illuminate\Support\Facades\URL::previous()}}"
                             label="Retour">Retour
    </x-filament::icon-button>
    <h2>Modifier la collection</h2>
    <div class="flex flex-col gap-6">
        {{$this->form}}
        {{$this->table}}
    </div>
</div>
