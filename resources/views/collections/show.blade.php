@extends('layout')
@section('content')
    <h1 class="text-3xl font-semibold">{{$collection->name}}</h1>
    <p>{!! str($collection->description)->sanitizeHtml() !!}</p>
    @livewire('show-collection', [$collection])
@endsection
