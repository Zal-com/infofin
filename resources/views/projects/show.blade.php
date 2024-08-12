@extends('layout')
@section('og:title', $og_title)
@section('og:description', $og_description)
@section('og:image', $og_image)
@section('og:url', $og_url)
@section('og:type', $og_type)
@section('content')
    @livewireStyles
    <div class="flex justify-between items-center mb-4">
        {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('project', $project) }}
        @can('create', App\Models\Project::class)
            <x-filament::button icon="heroicon-s-pencil" tag="a"
                                href="{{ url(route('projects.edit', $project->id)) }}">
                Modifier
            </x-filament::button>
        @endcan
    </div>
    <x-project-show-info :project="$project"/>
    <x-filament::section.description class="pl-5">
        Dernière modification le {{ \Carbon\Carbon::make($project->updated_at)->format('d/m/Y') }}
        par {{ $project->poster->full_name() }}
    </x-filament::section.description>
    @livewireScripts
@endsection
