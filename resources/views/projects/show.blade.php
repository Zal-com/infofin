@extends('layout')
@section('content')
    {{\Diglactic\Breadcrumbs\Breadcrumbs::render('project', $project)}}
<h1>Description de l'appel</h1>
    <p>{{$project->title}}</p>
    <p>{{$project->organisation_id}}</p>
    <p>{{$project->deadline === '0000-00-00' ? 'N/A' :  date('d/m/Y', strtotime($project->deadline))}}</p>
    <p>{{$project->deadline_2 === '0000-00-00' ? 'N/A' :  date('d/m/Y', strtotime($project->deadline_2))}}</p>
@foreach($project->infoType as $infotype)
    <p>{{$infotype->Name}}</p>
@endforeach
    <p>Type de Programme</p>
    <p>{!! $project->short_description !!}</p>
    <p>{!! $project->long_description !!}</p>

    <p>Contact ULB</p>
    @foreach(json_decode($project->contact_ulb, true) as $contact_int )
        <p>{{$contact_int['name']}} / {{$contact_int['email']}} / {{$contact_int['phone']}} / {{$contact_int['address']}}</p>
    @endforeach

    @foreach(json_decode($project->contact_ext, true) as $contact_ext )
        <p>{{$contact_ext['name']}} / {{$contact_ext['email']}} / {{$contact_ext['phone']}} / {{$contact_ext['address']}}</p>
    @endforeach


    <p>Contact Externe</p>
@endsection
