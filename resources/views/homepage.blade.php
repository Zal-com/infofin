@extends('layout')
@section('content')
    <h1 class="">Bienvenue sur Infofin</h1>
    <h2>La base de données d'informations des sources de financement de la Recherche à l'ULB</h2>
    <div class="contain-content flex gap-8 flex-row justify-center">

        <div class="contain-content border-2 rounded-xl w-fit p-8">
            <p>Dans la base de données Infofin, vous retrouverez :</p>
            <ul class="list-disc list-inside">
                <li class="list-item">Appels à projets</li>
                <li class="list-item">Prix et distinctions</li>
                <li class="list-item">Financements de colloques</li>
                <li class="list-item">Bourses postdoctorales</li>
                <li class="list-item">Financements de mobilité</li>
                <li class="list-item">Financements d'activités de networking</li>
                <li class="list-item">Séances de formations et d'informations</li>
            </ul>
        </div>

        <div class="w-5/12">
            <x-card :title="'Se connecter'"
                    :button1="'Connexion externe'"
                    :url1="'https://cvchercheurstest.ulb.be/?AC=1&externe=1'"
                    :button2="'Se connecter'"
                    :url2="'https://infofin.ulb.ac.be/login.php'"
            />

            <x-card :title="'Liste des projets'"
                    :desc="'Retrouvez ici la liste des projets encodés dans la base de données'"
                    :button1="'Voir les projets'"
                    :url1="'https://cvchercheurstest.ulb.be/?AC=999'"

            />
        </div>
    </div>
    <div class="p-2">
        <form method="POST" class="flex justify-center gap-2">
            @csrf
            <input class="w-9/12 border rounded px-5 py-2" type="text" placeholder="Rechercher un projet...">
            <button type="submit" class="w-2/12 bg-blue-400 p-2 rounded text-white">Rechercher</button>
        </form>
    </div>
    {{--
        <x-card :title="'Recherche de projets'"
                :desc="'Effectuez une recherche dans la base de données et retrouvez un projet spécifique'"
                :button1="'Rechercher'"
                :url1="'https://cvchercheurstest.ulb.be/?AC=999&VP=1&ST=1&ACTIVE=1'"
        />
    --}}
    <x-card :title="'Enregistrement à Infofin'"
            :desc="'Enregistrez-vous à lma base de donénes Infofin pour recevoir un mail hebdomadaire reprendant les nouveaux appels qui correspondent à vos filtres'"
            :button1="'S\'enregistrer'"
            :url1="'https://cvchercheurstest.ulb.be/?AC=100'"
    />
@endsection
