@extends('layout')
@section('content')
    <div class="flex flex-col items-center">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')"/>

        <form method="POST" action="{{ route('login') }}"
              class="mt-10 flex flex-col sm:justify-center items-center sm:pt-0 bg-gray-50 w-1/3 p-10 shadow rounded">
            @csrf

            <!-- Email Address -->
            <div class="mt-10 w-full">
                <x-input-label for="email" :value="__('Email')"/>
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                              required
                              autofocus autocomplete="username"/>
                <x-input-error :messages="$errors->get('email')" class="mt-2"/>
            </div>

            <!-- Password -->
            <div class="mt-4 w-full">
                <x-input-label for="password" :value="__('Password')"/>

                <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required autocomplete="current-password"/>

                <x-input-error :messages="$errors->get('password')" class="mt-2"/>
            </div>

            <!-- Remember Me -->
            <div class="flex mt-4 w-full justify-between">
                <label for="remember_me" class="pl-2 inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                           name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="flex flex-col gap-2 items-center mt-4 w-full justify-center">
                <x-primary-button class="ms-3 w-2/3 justify-center">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
            <div id="or">OU</div>
            <a href="{{ route('login.cas') }}" class="ms-3 w-2/3">
                <x-secondary-button class="flex gap-2 pl-0 pt-0 pb-0 w-full">
                    <img src="{{url('img/ulb_logo_simple.png')}}" height="36" width="36" class="rounded-l">
                    <div class="flex w-full justify-center">Se connecter avec l'ULBID</div>
                </x-secondary-button>
            </a>
            <a href="{{ route('register') }}" class="ms-3 w-2/3">
                <x-secondary-button class="flex w-full justify-center">
                    Créer un compte
                </x-secondary-button>
            </a>
        </form>


    </div>
@endsection
