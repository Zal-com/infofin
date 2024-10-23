<div>
    <x-filament::icon-button tag="a" icon="heroicon-arrow-uturn-left" href="{{back()}}" label="Retour"/>
    <h2>Modifier la collection</h2>
    <div class="flex flex-col gap-6">
        {{$this->form}}
        {{$this->table}}
    </div>
</div>
