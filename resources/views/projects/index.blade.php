@extends('layout')

@section('content')
    <div class="w-100 flex justify-between">
        <h2>Liste des projets</h2>
        @can('create project')
            <div x-data="{ open: false }" class="relative inline-block text-left">
                <x-filament::button @click="open = !open" color="primary" icon="heroicon-s-plus">
                    Ajouter
                </x-filament::button>

                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <a href="{{ url(route('projects.create')) }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <x-heroicon-s-briefcase class="w-5 h-5 inline mr-2"/>
                            Projet
                        </a>
                        <a href="{{route('info_session.create')}}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <x-heroicon-s-calendar class="w-5 h-5 inline mr-2"/>
                            Séance d'information
                        </a>
                    </div>
                </div>
            </div>
            {{--
            <div>
                <x-filament::button tag="a" color="info" href="{{ url(route('projects.create')) }}"
                                    icon="heroicon-s-plus">Nouvelle séance
                </x-filament::button>
                <x-filament::button tag="a" color="primary" href="{{ url(route('projects.create')) }}"
                                    icon="heroicon-s-plus">Nouveau
                    projet
                </x-filament::button>
            </div>
            --}}
        @endcan
    </div>
    <div>
        @livewire('list-projects')
    </div>
@endsection
