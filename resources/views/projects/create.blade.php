@extends('layout')
@section('content')
    @props(['draft'])
    <div class="py-4">
        <h2>Création d'un nouveau projet</h2>
        <div class="mt-5">
            @livewire('project-form', $draft)
        </div>
    </div>
@endsection
