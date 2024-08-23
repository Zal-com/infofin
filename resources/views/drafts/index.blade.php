@extends('layout')

@section('content')
    <div class="w-100 flex justify-between">
        <h2>Liste des brouillons</h2>
    </div>
    <div>
        @livewire('list-draft')
    </div>
@endsection
