@extends('layout')
@props(['user'])
@section('content')
    <div class="grid grid-cols-5 gap-4 mb-10" x-data="{ tab: 'infos' }">
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
        </x-filament::tabs>

        <x-filament::section class="col-span-4 row-span-2">
            <div x-show="tab === 'infos'" class="m-4">
                <x-filament::section.heading>
                    Infos personnelles
                </x-filament::section.heading>
                <div>
                    @livewire('profile-form')
                </div>

            </div>
            <div x-show="tab === 'drafts'" class="m-4">
                @livewire('user-drafts')
            </div>
            <div x-show="tab === 'appels'" class="m-4">
                Appels infofin
            </div>
        </x-filament::section>
    </div>
@endsection
