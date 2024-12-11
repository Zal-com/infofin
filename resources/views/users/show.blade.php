@extends('layout')
@props(['user'])
@section('content')

    <div class="grid grid-cols-6 gap-2 mb-10"
         x-data="{
            tab: '{{ session('defaultTab', 'interests') }}' }">
        <div class="sticky top-5"> <!-- Sticky avec hauteur et overflow -->
            <x-filament::tabs class="flex-col max-h-min">
                <x-filament::tabs.item @click="tab = 'interests'" :alpine-active="'tab === \'interests\''"
                                       icon="heroicon-o-heart">
                    Mes centres d'intérêt
                </x-filament::tabs.item>
                <x-filament::tabs.item @click="tab = 'favorites'" :alpine-active="'tab === \'favorites\''"
                                       icon="heroicon-o-bookmark">
                    Mes favoris
                </x-filament::tabs.item>
                @can('create collection')
                    <x-filament::tabs.item @click="tab = 'collections'" :alpine-active="'tab === \'collections\''"
                                           icon="heroicon-o-folder">
                        Mes collections
                    </x-filament::tabs.item>
                @endcan
                @can('create draft')
                    <x-filament::tabs.item @click="tab = 'drafts'" :alpine-active="'tab === \'drafts\''"
                                           icon="heroicon-o-pencil">
                        Mes brouillons
                    </x-filament::tabs.item>
                @endcan
                @can('create project')
                    <x-filament::tabs.item @click="tab = 'appels'" :alpine-active="'tab === \'appels\''"
                                           icon="heroicon-o-document-text">
                        Mes projets
                    </x-filament::tabs.item>
                @endcan
            </x-filament::tabs>
            @livewire('newsletter-subscription-component')
        </div>
        <x-filament::section class="col-span-5 row-span-2">
            <div x-show="tab === 'infos'" class="m-4">
                <x-filament::section.heading>
                    Infos personnelles
                </x-filament::section.heading>
                <div class="mt-4">
                    @livewire('profile-form')
                </div>
            </div>
            @can('create collection')
                <div x-show="tab === 'collections'" class="m-4">
                    <x-filament::section.heading class="mb-5 flex justify-between items-center">
                        Mes collections
                    </x-filament::section.heading>
                    @livewire('user-collection')
                </div>
            @endcan
            @can('create draft')
                <div x-show="tab === 'drafts'" class="m-4">
                    <x-filament::section.heading class="mb-5 flex justify-between items-center">
                        Mes brouillons
                        @can('create', \App\Models\Project::class)
                            <x-filament::button icon="heroicon-o-plus" class="m-0" tag="a"
                                                href="{{ route('projects.create') }}">
                                Créer un projet
                            </x-filament::button>
                        @endcan
                    </x-filament::section.heading>
                    @livewire('user-drafts')
                </div>
            @endcan
            @can('view own project')
                <div x-show="tab === 'appels'" class="m-4">
                    <x-filament::section.heading class="mb-5 flex justify-between items-center">
                        Mes projets Infofin
                        @can('create project', \App\Models\Project::class)
                            <x-filament::button icon="heroicon-o-plus" class="m-0" tag="a"
                                                href="{{ route('projects.create') }}">
                                Créer un projet
                            </x-filament::button>
                        @endcan
                    </x-filament::section.heading>
                    @livewire('user-projects')
                </div>
            @endcan
            <div x-show="tab === 'interests'" class="m-4">
                <x-filament::section.heading>
                    Centres d'intérêt
                </x-filament::section.heading>
                @livewire('user-interests')
            </div>
            <div x-show="tab === 'favorites'" class="m-4">
                <x-filament::section.heading class="mb-5">
                    Mes favoris
                </x-filament::section.heading>
                @livewire('user-favorites')
            </div>
        </x-filament::section>
    </div>
@endsection
