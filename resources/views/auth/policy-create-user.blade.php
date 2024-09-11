@extends('layouts.app')

@section('content')
    <div class="w-100 flex justify-between">
        <h2>Liste des projets</h2>

        <livewire:accept-privacy-policy :userDetails="$userDetails" />
    </div>
@endsection
