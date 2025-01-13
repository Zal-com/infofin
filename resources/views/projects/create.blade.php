@extends('layout')
@section('content')
    @props(['draft'])
    <div class="py-4">
        <h2 class="text-2xl font-semibold">Création d'un nouveau projet</h2>
        <div class="mt-5">
            @if(isset($draft))
                @livewire('project-form', ['draft' => $draft])
            @else
                @livewire('project-form')
            @endif

        </div>
    </div>
@endsection
