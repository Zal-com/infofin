@extends('layout')

@section('content')
    {{\Diglactic\Breadcrumbs\Breadcrumbs::render('projects')}}
    <div class="w-100 flex justify-between">
        <h2>Liste des projets</h2>
        <a href="{{url(route('projects.create'))}}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"><i class="fa fa-solid fa-plus pr-2"></i>
            Nouveau projet
        </a>
    </div>
    <div>
   @livewire('list-projects')
    </div>
@endsection
