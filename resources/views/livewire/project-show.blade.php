<div>
    <div class="flex flex-row justify-end gap-2 items-center mb-4">
        @canany(['edit own project', 'edit other project'])
            <x-filament::button icon="heroicon-s-pencil" tag="a"
                                href="{{ url(route('projects.edit', $project->id)) }}">
                Modifier
            </x-filament::button>
        @endcan
        @auth
            @if(\Illuminate\Support\Facades\Auth::user()->favorites->contains($project->id))
                <x-filament::icon-button icon="heroicon-s-heart" tooltip="Retirer des favoris" size="lg" outlined
                                         color="black" class="font-bold" wire:click="removeFromFavorites"/>
            @else
                <x-filament::icon-button icon="heroicon-o-heart" tooltip="Ajouter aux favoris" size="lg" outlined
                                         color="black" class="font-bold" wire:click="addToFavorites"/>
            @endif
        @endauth
        @can('create collection')
            <x-filament::dropdown placement="bottom-end">
                <x-slot name="trigger">
                    <x-filament::icon-button type="button" size="xl" color="primary"
                                             icon="heroicon-s-ellipsis-vertical"/>
                </x-slot>

                <x-filament::dropdown.list>


                    <x-filament::dropdown.list.item icon="heroicon-o-archive-box" color="danger"
                                                    wire:click="archiveProject">
                        Archiver
                    </x-filament::dropdown.list.item>
                </x-filament::dropdown.list>
            </x-filament::dropdown>
        @endcan
    </div>
    <x-project-show-info :project="$project"/>
    <x-filament::section.description class="pl-5">
        Dernière modification le {{ \Carbon\Carbon::make($project->updated_at)->format('d/m/Y') }}
        par {{ $project->poster->full_name() }}
    </x-filament::section.description>
    <x-filament::section.description class="pl-5">
        @auth
            @hasrole("contributor")
            Vues : {{ $project->visit_count }}
            @endrole
        @endauth
    </x-filament::section.description>
</div>
