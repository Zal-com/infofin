@extends('layout')
@section('content')
    <div>
        <div class="flex flex-row justify-between">
            <h2>Liste des sessions d'informations</h2>
            @can('create info_session')
                <x-filament::button color="primary" tag="a" href="{{route('info_session.create')}}"
                                    icon="heroicon-o-plus">
                    Ajouter
                </x-filament::button>
            @endcan
        </div>
        @livewire('list-info-session')
    </div>
@endsection

