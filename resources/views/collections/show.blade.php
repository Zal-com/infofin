@extends('layout')
@section('content')
    <h1>{{$collection->name}}</h1>
    <p>{!! str($collection->description)->sanitizeHtml() !!}</p>
    @livewire('show-collection', [$collection])
@endsection
