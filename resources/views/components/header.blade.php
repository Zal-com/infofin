<header>
    <a href="{{route('home')}}">
    <div class="flex h-[120px] p-4 w-[75%] m-auto">
        <div class="flex items-center">
            <img class="max-h-[100%] object-contain pr-4 border-r-2 border-blue-900" src="{{asset('img/ulb_logo.png')}}" alt="Logo de l'ULB"/>
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
        <div>
            @auth()
                <a href="#">Profil</a>
                <form method="post" action="{{route('logout')}}">@csrf<button type="submit">Déconnexion</button></form>
            @else
                <a href="{{route('login')}}">
                    Login
                </a>
            @endauth
            <a href="{{route('projects.index')}}">
                Liste des projets
            </a>

        </div>
    </nav>
</header>
