@extends('layout')

@section('content')
    @livewireStyles
    <div class="flex justify-between items-center mb-4">
        @can('create', App\Models\Project::class)
            <x-filament::button icon="heroicon-s-pencil" tag="a"
                                href="{{ url(route('projects.create') . '?record=' . $project['id']) }}">
                Modifier
            </x-filament::button>
        @endcan
    </div>
    <x-draft-show-info :project="$project"/>
    @livewireScripts
@endsection
