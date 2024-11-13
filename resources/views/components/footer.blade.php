<div class="mt-10">
    <div class="flex gap-0">
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #000000"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #e6027c"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #8fc149"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #0286b1"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #87888a"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #e62213"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #0269b5"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #6f4f9b"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #f9a823"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #0e4c9c"></span>
        <span class="w-[9.09091%] inline-block h-[7px]" style="background: #008438"></span>
    </div>
    <footer class="bg-blue-950 text-white flex sm:flex-row flex-col p-5 gap-5 min-h-[200px]">
        <div class="container m-auto flex flew-row">
            <a href="{{route('projects.index')}}" class="contain-content">
                <img src="{{ asset('img/ULB_logo_mono_blanc.png') }}" alt="ULB Logo">
            </a>

            <div class="flex flex-col justify-center w-full gap-5 sm:flex-row sm:gap-10">
                <!-- Groupe 1: Liens utiles -->
                <div class="flex flex-col gap-2">
                    <h4 class="font-semibold">
                        LIENS UTILES
                    </h4>
                    <ul class="flex flex-col gap-2 align-middle list-none">
                        <li class="flex items-center gap-1">
                            <x-filament::icon icon="heroicon-o-chevron-right"
                                              style="height: 18px; width: 18px;"/>
                            <a href="{{ route('agenda') }}" class="hover:text-slate-300">Agenda</a>
                        </li>
                        @can('view archives')
                            <li class="flex items-center gap-1">
                                <x-filament::icon icon="heroicon-o-chevron-right"
                                                  style="height: 18px; width: 18px;"/>
                                <a href="{{ route('projects.archive') }}" class="hover:text-slate-300">Archives</a>
                            </li>
                        @endcan
                        <li class="flex items-center gap-1">
                            <x-filament::icon icon="heroicon-o-chevron-right"
                                              style="height: 18px; width: 18px;"/>
                            <a href="{{ route('faq') }}" class="hover:text-slate-300">Foire aux questions</a>
                        </li>
                        <li class="flex items-center gap-1">
                            <x-filament::icon icon="heroicon-o-chevron-right"
                                              style="height: 18px; width: 18px;"/>
                            <a href="{{ route('privacy-policy') }}" class="hover:text-slate-300">Politique de
                                confidentialité</a>
                        </li>
                        <li class="flex items-center gap-1">
                            <x-filament::icon icon="heroicon-o-chevron-right"
                                              style="height: 18px; width: 18px;"/>
                            <a href="{{ route('projects.index') }}" class="hover:text-slate-300">Projets</a>
                        </li>
                        <li class="flex items-center gap-1">
                            <x-filament::icon icon="heroicon-o-chevron-right"
                                              style="height: 18px; width: 18px;"/>
                            <a href="{{ route('info_session.index') }}" class="hover:text-slate-300">Séances
                                d'information</a>
                        </li>
                    </ul>
                </div>
                <!-- Groupe 2: Profil -->
                <div class="flex flex-col gap-2">
                    @guest
                        <h4 class="font-semibold flex flew-row items-center gap-2">
                            CONNEXION
                        </h4>
                        <ul class="flex flex-col gap-2 align-middle list-none">
                            <li class="flex items-center gap-1">
                                <x-filament::icon icon="heroicon-o-chevron-right"
                                                  style="height: 18px; width: 18px;"/>
                                <a href="{{route('login.cas')}}"
                                   class="hover:text-slate-300">Connexion CAS</a>
                            </li>
                            <li class="flex items-center gap-1">
                                <x-filament::icon icon="heroicon-o-chevron-right"
                                                  style="height: 18px; width: 18px;"/>
                                <a href="{{route('register')}}"
                                   class="hover:text-slate-300">Créer un compte</a>
                            </li>
                            <li class="flex items-center gap-1">
                                <x-filament::icon icon="heroicon-o-chevron-right"
                                                  style="height: 18px; width: 18px;"/>
                                <a href="{{route('login')}}"
                                   class="hover:text-slate-300">Se connecter</a>
                            </li>
                        </ul>
                    @endguest
                    @auth
                        <h4 class="font-semibold flex flew-row items-center gap-2">
                            PROFIL
                        </h4>
                        <ul class="flex flex-col gap-2 align-middle list-none">
                            <li class="flex items-center gap-1">
                                <x-filament::icon icon="heroicon-o-chevron-right"
                                                  style="height: 18px; width: 18px;"/>
                                <a href="{{route('profile.show', \Illuminate\Support\Facades\Auth::id())}}"
                                   class="hover:text-slate-300">Mon profil</a>
                            </li>
                            <li class="flex items-center gap-1">
                                <x-filament::icon icon="heroicon-o-chevron-right"
                                                  style="height: 18px; width: 18px;"/>
                                <form method="post" action="{{route('logout')}}">
                                    @csrf
                                    <button class="hover:text-slate-300" type="submit">Se déconnecter</button>
                                </form>
                            </li>
                        </ul>
                    @endauth
                </div>

                <!-- Groupe 3: Contacts -->
                <div class="flex flex-col gap-2">
                    <h4 class="font-semibold flex flew-row items-center gap-2">
                        CONTACT
                    </h4>
                    <ul class="flex flex-col gap-2 align-middle list-none">
                        <li class="flex items-center gap-1">
                            <x-filament::icon icon="heroicon-o-envelope"
                                              style="height: 18px; width: 18px;"/>
                            <a href="mailto:guillaume.stordeur@ulb.be" class="hover:text-slate-300">Webmaster</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>
