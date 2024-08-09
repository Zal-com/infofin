<header class="mb-8">
    <a href="{{route('home')}}">
        <div class="container flex h-[120px] p-4 w-[75%] m-auto">
            <div class="flex items-center">
                <img class="max-h-[100%] object-contain pr-4 border-r-2 border-blue-900"
                     src="{{asset('img/ulb_logo.png')}}" alt="Logo de l'ULB"/>
            </div>
            <div class="text-blue-900 p-4">
                <h1 class="select-none">Infofin</h1>
            </div>
            <div class="flex-1">
                <img class="max-h-[100%] float-end" src="{{asset('img/header_img.png')}}" alt="Image décorative"/>
            </div>
        </div>
    </a>
    <nav>
        <div class="container m-auto flex justify-between items-center">
            <div>
            </div>
            <div class="flex gap-2">
                @auth()
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
