@extends('layout')

@section('content')
    <x-filament::section class="container m-auto bg-{{$color}}-900">
        <div class="flex flex-col items-center justify-center">
            <div class="text-6xl text-blue-500 mb-4">
                <x-filament::icon icon="{{$icon}}" style="height: 64px; width: 64px;"/>
            </div>
            <div class="text-center text-lg font-semibold text-gray-700">
                {{ $message }}
            </div>
        </div>
    </x-filament::section>
@endsection
