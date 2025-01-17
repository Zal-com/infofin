@extends('layout')
@section('content')
    @props(['project'])
    <div class="py-4">
        <h2 class="text-2xl font-semibold">Edition d'un projet</h2>
        <div class="mt-5">
            @livewire('project-edit-form', ['project' => $project])
        </div>
    </div>
@endsection
