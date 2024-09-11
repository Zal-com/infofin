@extends('layout')
@section('content')
    <div class="grid grid-cols-5 gap-4 mb-5">
        <x-filament::section class="col-span-4 row-span-2">
            <h1 class="font-bold text-4xl my-1">{{$info_session->title ?? ''}}</h1>
            <p class="text-md italic">{{$info_session->organisation->title ?? ''}}</p>
            <div class="markdown">
                <x-filament::section.description class="my-3 text-justify">
                    {!! $info_session->description !!}
                </x-filament::section.description>
            </div>
        </x-filament::section>
        <div class="flex flex-col gap-4 sticky top-5">
            <x-filament::section>
                <x-filament::section.heading>
                    <h3 class="font-semibold">Date & heure</h3>
                </x-filament::section.heading>
                <x-filament::section.description class="mt-4">
                    <p class="flex flex-row items-center gap-2">
                        <x-filament::icon size="6" class="max-h-[24px] max-w-[24px]" icon="heroicon-o-calendar-days"/>
                        {{\Carbon\Carbon::make($info_session->session_datetime)->format('d/m/Y')}}
                    </p>
                    <p class="flex flex-row items-center gap-2">
                        <x-filament::icon size="6" class="max-h-[24px] max-w-[24px]" icon="heroicon-o-clock"/>
                        {{\Carbon\Carbon::make($info_session->session_datetime)->format('H:i')}}
                    </p>
                </x-filament::section.description>
            </x-filament::section>
            {{-- TODO Ajouter au calendrier
            <x-filament::section>
                Ajouter au calendrier
            </x-filament::section>
            --}}
        </div>
    </div>

@endsection
