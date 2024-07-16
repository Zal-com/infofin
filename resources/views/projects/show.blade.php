@extends('layout')
@section('content')
    {{\Diglactic\Breadcrumbs\Breadcrumbs::render('project', $project)}}
    <x-project-show-info :project="$project"/>
    <x-filament::section.description>
        Dernière modification le {{\Carbon\Carbon::make($project->updated_at)->format('d/m/Y')}}
        par {{$project->poster->full_name()}}
    </x-filament::section.description>
@endsection
