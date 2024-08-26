@extends('layout')
@section('content')
    <div class="container mx-auto mt-8">
        <div class="flex flex-row justify-between">
            <h2 class="mb-4">Calendrier de projets</h2>
            <div class="flex flex-row gap-2 items-center">
                <div class="h-5 w-5 rounded-md" style="background: crimson">
                </div>
                Projet majeur
            </div>
        </div>
        @livewire('calendar-component')
    </div>
@endsection
