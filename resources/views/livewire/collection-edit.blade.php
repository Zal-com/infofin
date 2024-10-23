<div>
    <x-filament::icon-button icon="heroicon-o-arrow-uturn-left"
                             href="{{\Illuminate\Support\Facades\URL::previous()}}"
                             label="Retour"
    />
    <h2 class="my-4">Modifier la collection</h2>
    <div class="flex flex-col gap-6">
        {{$this->form}}
        {{$this->table}}
    </div>
</div>
