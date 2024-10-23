<div>
    <div class="flex flex-col w-fit hover:underline hover:text-blue-500">
        <x-filament::icon-button tag="a" icon="heroicon-o-arrow-uturn-left"
                                 href="{{\Illuminate\Support\Facades\URL::previous()}}"
        />
        <span>Retour</span>
    </div>

    <h2 class="my-4">Modifier la collection</h2>
    <div class="flex flex-col gap-6">
        {{$this->form}}
        {{$this->table}}
    </div>
</div>
