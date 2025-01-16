@extends('layout')
@section('content')
    <h2 class="mb-4 text-2xl font-semibold">Modification d'une session d'information</h2>
    @livewire('info-session-edit-form', ['info_session' => $info_session])
@endsection
