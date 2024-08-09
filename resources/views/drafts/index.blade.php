@extends('layout')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('projects') }}
    <div class="w-100 flex justify-between">
        <h2>Liste des brouillons</h2>
    </div>
    <div>
        @livewire('list-draft')
    </div>
@endsection
