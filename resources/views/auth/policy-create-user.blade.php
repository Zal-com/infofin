@extends('layout')

@section('content')
    <div class="w-100 flex justify-between">
        <livewire:accept-privacy-policy :userDetails="$userDetails" />
    </div>
@endsection
