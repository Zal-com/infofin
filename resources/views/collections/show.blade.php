@extends('layout')
@section('content')
    <h1>{{$collection->name}}</h1>
    <p>{{$collection->description}}</p>
    @livewire('show-collection', [$collection])
@endsection
