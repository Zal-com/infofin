@extends('layout')
@section('og:title', $og_title)
@section('og:description', $og_description)
@section('og:image', $og_image)
@section('og:url', $og_url)
@section('og:type', $og_type)
@section('content')
    @livewire('project-show', [$project])
@endsection
