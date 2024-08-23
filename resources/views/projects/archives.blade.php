@extends('layout')

@section('content')
    <div class="w-100 flex justify-between">
        <h2>Archives des projects</h2>
    </div>
    <div>
        @livewire('archives-project')
    </div>
@endsection
