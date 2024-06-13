@extends('layout')
@section('content')
    {{\Diglactic\Breadcrumbs\Breadcrumbs::render('project', $project)}}
    <x-project-show-info :project="$project"/>
@endsection
