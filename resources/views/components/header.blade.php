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
                    <a href="{{route('profile.show')}}"
                       class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md
                       font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-gray-700
                       active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                       transition ease-in-out duration-150"
                    >
                        <i class="fa-solid fa-user pr-2"></i>
                        Profil
                    </a>
                    <form method="post" action="{{route('logout')}}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-gray-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            <i class="fa-solid fa-right-from-bracket pr-2"></i>
                            Déconnexion
                        </button>
                    </form>
                @else
                    @if(\Illuminate\Support\Facades\Route::currentRouteName() !== 'login')
                        <a href="{{route('login')}}"
                           class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-gray-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fa-solid fa-right-to-bracket pr-2"></i>
                            Se connecter
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>
</header>
