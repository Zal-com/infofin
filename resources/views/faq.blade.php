@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto py-12">
        <h1 class="text-3xl font-semibold text-center mb-8">Foire aux questions</h1>

        <x-zeus-accordion::accordion>
            <x-zeus-accordion::accordion.item
                :isIsolated="true"
                :label="'J\'ai perdu mon mot de passe'"
                icon="heroicon-o-chevron-right"
            >
                <div class="bg-white p-4">
                    Rendez-vous sur cette <a href="{{ route('password.request') }}"
                                             class="text-blue-600 hover:text-blue-900">page</a> et saisissez votre
                    adresse
                    email. Vous recevrez ensuite un email contenant les instructions pour réinitialiser votre mot de
                    passe.
                </div>
            </x-zeus-accordion::accordion.item>

            <x-zeus-accordion::accordion.item
                :isIsolated="true"
                :label="'Je ne suis plus membre interne de l\'ULB'"
                icon="heroicon-o-chevron-right"
            >
                <div class="bg-white p-4">
                    Pas d'inquiétude, vous pouvez toujours vous connecter en utilisant votre ancienne adresse interne
                    avec le
                    mot de passe que vous avez enregistré dans votre profil. N'oubliez pas de mettre à jour votre
                    adresse email
                    une fois connecté.
                </div>
            </x-zeus-accordion::accordion.item>

            <x-zeus-accordion::accordion.item
                :isIsolated="true"
                :label="'Je n\'ai plus accès ni à mon email, ni à mon mot de passe'"
                icon="heroicon-o-chevron-right"
            >
                <div class="bg-white p-4">
                    Si vous n'avez plus accès à votre email et que vous ne vous souvenez plus de votre mot de passe,
                    deux solutions
                    s'offrent à vous : vous pouvez contacter <a href="mailto:guillaume.stordeur@ulb.be"
                                                                class="text-blue-600 hover:text-blue-900">notre
                        administrateur</a> ou bien créer un nouveau compte via cette <a href="{{ route('register') }}"
                                                                                        class="text-blue-600 hover:text-blue-900">page
                        d'inscription.</a>
                </div>
            </x-zeus-accordion::accordion.item>
        </x-zeus-accordion::accordion>
    </div>
@endsection
