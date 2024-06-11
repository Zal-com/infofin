@extends('layout')
@section('content')
    {{\Diglactic\Breadcrumbs\Breadcrumbs::render('project', $project)}}
<h1>Description de l'appel</h1>
    <p>{{$project->Name}}</p>
    <p>{{$project->Organisation}}</p>
    <p>{{$project->Deadline === '0000-00-00' ? 'N/A' :  date('d/m/Y', strtotime($project->Deadline))}}</p>
    <p>{{$project->Deadline2 === '0000-00-00' ? 'N/A' :  date('d/m/Y', strtotime($project->Deadline2))}}</p>
@foreach($project->infoType as $infotype)
    <p>{{$infotype->Name}}</p>
@endforeach
    <p>Type de Programme</p>
    <p>{!! $project->ShortDescription !!}</p>
    <p>{!! $project->LongDescription !!}</p>
    <p>Contact ULB</p>
    <p>Contact Externe</p>
@endsection
