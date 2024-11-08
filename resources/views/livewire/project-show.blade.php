<div>
    <div class="flex flex-row justify-end gap-2 items-center mb-4">
        @canany(['edit own project', 'edit other project'])
            <x-filament::button icon="heroicon-s-pencil" tag="a"
                                href="{{ url(route('projects.edit', $project->id)) }}">
                Modifier
            </x-filament::button>
        @endcan
        @auth
            @php
                $isFavorite = \Illuminate\Support\Facades\Auth::user()->favorites->contains($project->id);
                $icon = $isFavorite ? 'heroicon-s-bookmark' : 'heroicon-o-bookmark';
                $tooltip = $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris';
                $action = $isFavorite ? 'removeFromFavorites' : 'addToFavorites';
            @endphp
            @hasanyrole('contributor|admin')
            <x-filament::icon-button
                :icon="$icon"
                :tooltip="$tooltip"
                size="lg"
                outlined
                color="black"
                class="font-bold"
                wire:click="{{ $action }}"
            />
        @else
            <x-filament::button
                :icon="$icon"
                size="lg"
                color="primary"
                class="font-bold"
                wire:click="{{ $action }}"
            >
                Favoris
            </x-filament::button>
            @endhasanyrole
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
    <div class="flex justify-start divide-x">
        <x-filament::section.description class="px-5">
            Dernière modification le {{ \Carbon\Carbon::make($project->updated_at)->format('d/m/Y') }}
            par {{ $project->poster->full_name() }}
        </x-filament::section.description>
        @auth
            @hasrole('contributor|admin')
            <x-filament::section.description class="px-5">
                Vues : {{ $project->visit_count }}
            </x-filament::section.description>
            @endrole
        @endauth
        @auth
            @hasanyrole('contributor|admin')
            <x-filament::section.description class="px-5">
                Vues depuis le mail : {{ $project->visit_count_email }}
            </x-filament::section.description>
            @endrole
        @endauth
    </div>

</div>
