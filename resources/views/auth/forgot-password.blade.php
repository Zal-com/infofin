@extends('layout')
@section('content')
    <div class="flex flex-col items-center">
        <div class="mt-10 flex flex-col sm:justify-center items-center sm:pt-0 bg-gray-50 w-1/3 p-10 shadow rounded">
            <div class="pt-10 mb-4 text-sm text-gray-600">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')"/>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')"/>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                        />
                    </x-filament::input.wrapper>
                   
                    <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-filament::button color="primary" type="submit">
                        {{ __('Email Password Reset Link') }}
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
@endsection
