@extends('layout')
@section('content')
    <div class="container mx-auto mt-8">
        <div class="flex flex-row justify-between">
            <h2 class="mb-4">Calendrier de projets</h2>
            <div class="flex flex-row gap-2 items-center">
                <div class="flex flex-row gap-2 items-center mr-4">
                    <div class="h-5 w-5 rounded-md bg-blue-500"></div>
                    Projet
                </div>
                <div class="flex flex-row gap-2 items-center mr-4">
                    <div class="h-5 w-5 rounded-md bg-green-500"></div>
                    Séance d'informations
                </div>
                @hasanyrole('contributor|admin')
                <div class="flex flex-row gap-2 items-center">
                    <div class="h-5 w-5 rounded-md" style="background: crimson"></div>
                    Projet majeur
                </div>
                @endhasanyrole
            </div>
        </div>
        @livewire('calendar-component')
    </div>
@endsection
