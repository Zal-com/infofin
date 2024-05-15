@extends('layout')
@section('content')
    @foreach ($projects as $project)
        <p>This is project : {{$project->Name}}</p>
    @endforeach
@endsection