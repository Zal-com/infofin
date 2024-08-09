@extends('layout')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('projects') }}
    <div class="w-100 flex justify-between">
        <h2>Liste des projets</h2>
        @can('create', App\Models\Project::class)
            <x-filament::button tag="a" color="primary" href="{{ url(route('projects.create')) }}"
                                icon="heroicon-s-plus">Nouveau
                projet
            </x-filament::button>
        @endcan
    </div>
    <div>
        @livewire('list-projects')
    </div>
@endsection
