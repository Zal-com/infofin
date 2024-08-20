<footer class="bg-blue-950 text-white flex sm:flex-row flex-col p-5 justify-between gap-5">
    <div class="contain-content">
        <img src="{{ asset('img/ULB_logo_mono_blanc.png') }}" alt="ULB Logo">
    </div>
    <div class="flex flex-col gap-5 sm:flex-row sm:gap-10">

        <!-- Groupe 1: Liens vers les projets et archives -->
        <div class="flex flex-col gap-2">
            <div class="flex justify-center align-middle text-center">
                <a href="{{ route('projects.archive') }}">
                    <p class="hover:text-slate-300">Archives</p>
                </a>
            </div>
            <div class="flex justify-center align-middle text-center">
                <a href="{{ route('projects.index') }}">
                    <p class="hover:text-slate-300">Projets</p>
                </a>
            </div>
        </div>

        <!-- Groupe 2: Liens vers la privacy policy et la FAQ -->
        <div class="flex flex-col gap-2">
            <div class="flex justify-center align-middle text-center">
                <a href="{{ url('/privacy-policy') }}">
                    <p class="hover:text-slate-300">Politique de confidentialité</p>
                </a>
            </div>
            <div class="flex justify-center align-middle text-center">
                <a href="{{ url('/faq') }}">
                    <p class="hover:text-slate-300">Foire aux questions</p>
                </a>
            </div>
        </div>

        <!-- Groupe 3: Liens de connexion/inscription (pour les invités) -->
        @guest
            <div class="flex flex-col gap-2">
                <div class="flex justify-center align-middle text-center">
                    <a href="{{ route('register') }}">
                        <p class="hover:text-slate-300">Créer un compte</p>
                    </a>
                </div>
                <div class="flex justify-center align-middle text-center">
                    <a href="{{ route('login') }}">
                        <p class="hover:text-slate-300">Connexion</p>
                    </a>
                </div>
                <div class="flex justify-center align-middle text-center">
                    <a href="{{ route('password.request') }}">
                        <p class="hover:text-slate-300">Mot de passe oublié</p>
                    </a>
                </div>
            </div>
        @endguest

        <!-- Groupe 4: Liens utilisateur connectés (profil, utilisateurs, déconnexion) -->
        @auth
            <div class="flex flex-col gap-2">
                <div class="flex justify-center align-middle text-center">
                    <a href="{{ route('profile.show') }}">
                        <p class="hover:text-slate-300">Profil</p>
                    </a>
                </div>
                <div class="flex justify-center align-middle text-center">
                    <form method="post" action="{{route('logout')}}">
                        @csrf
                        <button class="hover:text-slate-300" type="submit">Déconnexion</button>
                    </form>
                </div>
            </div>
        @endauth

        <!-- Groupe 5: IT Help -->
        <div class="flex justify-center align-middle text-center">
            <button class="flex justify-center align-middle text-center bg-blue-900 p-2 max-w-[8rem] gap-2">
                <div class="flex items-center m-auto justify-center">
                    <i class="fa-regular fa-circle-question fa-2xl" style="color: #ffffff;"></i>
                </div>
                <div class="flex items-center m-auto justify-center">
                    <p>IT Help</p>
                </div>
            </button>
        </div>
    </div>
</footer>
