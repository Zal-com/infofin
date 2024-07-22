@extends('layout')
@props(['user'])
@section('content')

    <div class="grid grid-cols-6 gap-2 mb-10" x-data="{ tab: localStorage.getItem('activeTab') || 'infos' }"
         x-init="$watch('tab', value => localStorage.setItem('activeTab', value))">
        <x-filament::tabs class="flex-col max-h-min sticky top-5 row-span-1">
            <x-filament::tabs.item @click="tab = 'infos'" :alpine-active="'tab === \'infos\''">
                Informations personnelles
            </x-filament::tabs.item>
            <x-filament::tabs.item @click="tab = 'drafts'" :alpine-active="'tab === \'drafts\''">
                Mes brouillons
            </x-filament::tabs.item>
            <x-filament::tabs.item @click="tab = 'appels'" :alpine-active="'tab === \'appels\''">
                Mes appels
            </x-filament::tabs.item>
            <x-filament::tabs.item @click="tab = 'interests'" :alpine-active="'tab === \'interests\''">
                Mes centres d'intérêt
            </x-filament::tabs.item>
        </x-filament::tabs>

        <x-filament::section class="col-span-5 row-span-2">
            <div x-show="tab === 'infos'" class="m-4">
                <x-filament::section.heading>
                    Infos personnelles
                </x-filament::section.heading>
                <div class="mt-4">
                    @livewire('profile-form')
                </div>
            </div>
            <div x-show="tab === 'drafts'" class="m-4">
                <x-filament::section.heading>
                    Mes brouillons
                </x-filament::section.heading>
                @livewire('user-drafts')
            </div>
            <div x-show="tab === 'appels'" class="m-4">
                <x-filament::section.heading>
                    Appels Infofin
                </x-filament::section.heading>
                @livewire('user-projects')
            </div>
            <div x-show="tab === 'interests'" class="m-4">
                <x-filament::section.heading>
                    Centres d'intérêt
                </x-filament::section.heading>
                @livewire('user-interests')
            </div>
        </x-filament::section>
    </div>
@endsection
