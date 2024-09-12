@extends('layout')
@section('content')
    @canany(['edit others info_session', 'edit own info_session'])
        <div class="flex flex-row mb-4 w-full justify-end">
            <x-filament::button icon="heroicon-o-pencil" color="primary" tag="a"
                                href="{{route('info_session.edit', $info_session->id)}}">
                Modifier
            </x-filament::button>
        </div>
    @endcanany
    <div class="grid grid-cols-5 gap-4 mb-5">
        <x-filament::section class="col-span-4 row-span-1 max-h-fit">
            <h1 class="font-bold text-4xl my-1">{{$info_session->title ?? ''}}</h1>
            <p class="text-md italic">{{$info_session->organisation->title ?? ''}}</p>
            <div class="markdown">
                <x-filament::section.description class="my-3 text-justify">
                    {!! $info_session->description !!}
                </x-filament::section.description>
            </div>
        </x-filament::section>
        <div class="flex flex-col gap-4 sticky top-5 row-span-2">
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
            @if(in_array($info_session->session_type, [1,2]))
                <div>
                    <iframe class="rounded-2xl"
                            width="100%"
                            height="450"
                            style="border:0"
                            loading="lazy"
                            allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDEaaG9fGm7Q3g2cw63TK0CMs3YAYyVeX0&q={{ urlencode($info_session->location)}}">
                    </iframe>
                </div>

            @elseif(in_array($info_session->session_type, [0,2]))
                <x-filament::section>
                    <x-filament::section.heading>
                        <h3 class="font-semibold">URL de la session</h3>
                    </x-filament::section.heading>
                    <x-filament::section.description>
                        @if($info_session->url)
                            <x-filament::link tag="a"
                                              href="{{ $info_session->url }}"
                                              icon="heroicon-o-arrow-top-right-on-square"
                                              iconPosition="before"
                                              label="{{ $info_session->url }}"><span
                                    class="truncate">{{ $info_session->url }}</span>
                            </x-filament::link>
                        @else
                            Pas de lien communiqué
                        @endif

                    </x-filament::section.description>
                </x-filament::section>
            @endif
            {{-- TODO Ajouter au calendrier
            <x-filament::section>
                Ajouter au calendrier
            </x-filament::section>
            --}}
        </div>
    </div>

@endsection
