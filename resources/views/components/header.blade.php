<header class="mb-5 shadow-sm p-4 w-screen">
    <nav class="flex flex-row justify-between">
        <a href="{{route('home')}}">
            <div class="flex flex-row gap-4 justify-start">
                <img src="{{asset('img/ulb_logo.png')}}" class="h-14 border-r-2 pr-3 border-blue-900">
                <h1 class="text-blue-900">Infofin</h1>

            </div>
        </a>
        <div class="flex flex-row justify-evenly gap-6">
            <x-filament::link tag="a" href="{{route('projects.index')}}">Projets</x-filament::link>
            <x-filament::link tag="a" href="{{url('/agenda')}}">Agenda</x-filament::link>
        </div>
        @guest()
            <div class="flex flex-row justify-end gap-4 p-3">
                <x-filament::link tag="a" href="{{route('register')}}">S'enregistrer</x-filament::link>
                <x-filament::button tag="a" href="{{route('login')}}">Se connecter</x-filament::button>
            </div>
        @endguest
        @auth
            <div class="flex flex-row justify-end gap-4 p-3">
                <x-filament::avatar
                    src="https://ui-avatars.com/api/?name={{Auth::user()->getAvatarInitials()}}&bold=true&color=FFFFFF&background=9845f9"
                />
            </div>
        @endauth
    </nav>

</header>
{{--
<header class="mb-8">
    <a href="{{route('home')}}">
        <div class="container flex h-[120px] p-4 w-[75%] m-auto">
            <div class="flex items-center">
                <img class="max-h-[100%] object-contain pr-4 border-r-2 border-blue-900"
                     src="{{asset('img/ulb_logo.png')}}" alt="Logo de l'ULB"/>
                <div class="text-blue-900 p-4">
                    <h1 class="select-none font-bold text-5xl items-center">Infofin</h1>
                </div>
            </div>
            <div class="flex-1">
                <img class="max-h-[100%] float-end" src="{{asset('img/header_img.png')}}"
                     alt="Image décorative"/>
            </div>
        </div>
    </a>
    <nav>
        <div class="container m-auto flex justify-between items-center">
            <div>
            </div>
            <div class="flex gap-2">
                @auth()
                    @role('admin')
                    <x-filament::button tag="a" href="{{url('/admin')}}" color="primary"
                                        icon="heroicon-o-chart-bar-square">Administration
                    </x-filament::button>
                    @endrole
                    <x-filament::button tag="a" href="{{route('profile.show')}}" color="primary"
                                        icon="heroicon-o-user">Profil
                    </x-filament::button>

                    <form method="post" action="{{route('logout')}}">
                        @csrf
                        <x-filament::button type="submit" icon="heroicon-o-arrow-right-start-on-rectangle"
                                            color="secondary">Déconnexion
                        </x-filament::button>
                    </form>
                @else
                    @if(\Illuminate\Support\Facades\Route::currentRouteName() !== 'login')
                        <x-filament::button href="{{route('login')}}" tag="a"
                                            icon="heroicon-o-arrow-right-end-on-rectangle"
                                            color="primary">Se connecter
                        </x-filament::button>
                    @endif
                @endauth
            </div>
        </div>
    </nav>
</header>
--}}
