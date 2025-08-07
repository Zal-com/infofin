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
                    Pas de panique ! Votre compte n'est pas perdu. Rendez-vous sur <a
                        href="https://support.ulb.be/web/support/-/j-ai-oublie-mon-ulbid.-ou-puis-je-le-retrouver-"
                        class="text-blue-600 hover:text-blue-900">cette page</a> et suivez les instructions pour
                    récupérer ou réinitialiser votre mot de passe.
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
                    mot de passe que vous avez enregistré dans votre profil.
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
                    s'offrent à vous : vous pouvez contacter <a href="mailto:support@ulb.be"
                                                                class="text-blue-600 hover:text-blue-900">le support
                        ULB</a> ou bien créer un nouveau compte via cette <a
                        href="https://monulb.ulb.be/fr/web/monulb/sign-up/"
                        class="text-blue-600 hover:text-blue-900">page
                        d'inscription.</a>
                </div>
            </x-zeus-accordion::accordion.item>
        </x-zeus-accordion::accordion>
    </div>
@endsection
