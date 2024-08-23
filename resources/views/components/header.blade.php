<header class="mb-5 shadow-sm p-4 w-full">
    <nav class="flex flex-col md:flex-row justify-between items-center">
        <!-- Section gauche -->
        <a href="{{ route('home') }}" class="flex flex-row gap-4 items-center">
            <img src="{{ asset('img/ulb_logo.png') }}" class="h-14 border-r-2 pr-3 border-blue-900">
            <h1 class="text-blue-900">Infofin</h1>
        </a>

        <!-- Conteneur central -->
        <div class="mt-4 md:mt-0 md:absolute md:left-1/2 md:transform md:-translate-x-1/2 flex flew-col gap-4">
            <x-filament::tabs.item
                tag="a"
                href="{{ route('projects.index') }}"
                :active="request()->routeIs('projects.index')">
                Projets
            </x-filament::tabs.item>

            <x-filament::tabs.item
                tag="a"
                href="{{ route('agenda') }}"
                :active="request()->routeIs('agenda')">
                Agenda
            </x-filament::tabs.item>
        </div>

        <!-- Section droite avec dropdown pour l'avatar -->
        <div class="flex flex-row justify-end gap-4 p-3 mt-4 md:mt-0 relative">
            @guest
                <x-filament::link tag="a" href="{{ route('register') }}">S'enregistrer</x-filament::link>
                <x-filament::button tag="a" href="{{ route('login') }}">Se connecter</x-filament::button>
            @endguest

            @auth
                <!-- Avatar as dropdown trigger -->
                <div @click.away="open = false" class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="focus:outline-none">
                        <img
                            src="https://ui-avatars.com/api/?name={{ Auth::user()->getAvatarInitials() }}&bold=true&color=FFFFFF&background=9845f9"
                            alt="{{ Auth::user()->name }}"
                            class="h-10 w-10 rounded-full cursor-pointer"
                        />
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open"
                         class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                        <a href="{{ route('profile.show') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Mon Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </nav>
</header>
