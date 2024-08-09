@extends('layout')
@section('content')
    @livewireStyles
    <div class="flex justify-between items-center mb-4">
        {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('project', $project) }}
        @can('create', App\Models\Project::class)
            <a href="{{ url(route('projects.edit', $project->id)) }}"
               class="inline-flex items-center px-4 py-2 bg-orange-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 focus:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fa fa-solid fa-plus pr-2"></i>
                Edit
            </a>
        @endcan
    </div>
    <x-project-show-info :project="$project"/>
    <x-filament::section.description class="pl-5">
        Dernière modification le {{ \Carbon\Carbon::make($project->updated_at)->format('d/m/Y') }}
        par {{ $project->poster->full_name() }}
    </x-filament::section.description>
    @livewireScripts
@endsection
