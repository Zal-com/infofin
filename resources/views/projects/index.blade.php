@extends('layout')

@section('content')
    {{\Diglactic\Breadcrumbs\Breadcrumbs::render('projects')}}
    <livewire:index-projects/>
@endsection
