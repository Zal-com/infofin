<div>
    <x-filament::icon-button tag="a" icon="heroicon-o-arrow-uturn-left"
                             href="{{\Illuminate\Support\Facades\URL::previous()}}"
    >RetourÒÒ
    </x-filament::icon-button>
    <h2 class="my-4">Modifier la collection</h2>
    <div class="flex flex-col gap-6">
        {{$this->form}}
        {{$this->table}}
    </div>
</div>
