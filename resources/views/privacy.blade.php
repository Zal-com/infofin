@extends('layout')

@section('content')
    <div class="container mx-auto px-4 py-8 text-justify max-w-[80ch]">
        <h1 class="text-3xl font-bold mb-6">Protection des données à caractère personnel</h1>

        <p class="mb-4">
            L’Université libre de Bruxelles – avenue Roosevelt 50, 1050 Bruxelles –, ci-après « l’ULB », est le
            responsable du traitement des données à caractère personnel vous concernant collectées et traitées dans le
            cadre de votre utilisation d’Infofin.
        </p>

        <h2 class="text-2xl font-semibold mb-4">Utilisation par des externes à l’ULB</h2>
        <p class="mb-4">
            L’utilisation d’Infofin requiert la création d’un compte ULB. Les informations quant au traitement de vos
            données dans ce cadre sont fournies lors de la procédure de création. Une fois le compte créé, vous pourrez
            utiliser Infofin comme tout membre de l’ULB. Si vous demandez la suppression de votre compte, vous perdrez
            l’accès à Infofin.
        </p>

        <h2 class="text-2xl font-semibold mb-4">Utilisation par les membres de l’ULB</h2>
        <p class="mb-4">
            En tant que membres du personnel de l’ULB, vous pouvez utiliser les services d’Infofin en vous connectant à
            l’aide de votre ULBID et en suivant la procédure MFA (ou en utilisant votre mot de passe le cas échéant).
        </p>

        <h2 class="text-2xl font-semibold mb-4">Services Infofin</h2>
        <p class="mb-4">
            Lors de votre première connexion à Infofin, vous déterminerez vos intérêts spécifiques. Votre adresse email
            sera utilisée pour vous communiquer les informations relatives aux financements liés à vos centres
            d’intérêts. Ces choix peuvent être modifiés à tout moment en vous connectant à votre profil d’utilisateur.
        </p>
        <p class="mb-4">
            Vous pouvez également vous désinscrire de l’envoi d’email à tout moment via votre profil utilisateur ou en
            cliquant sur le lien de désinscription qui accompagnera chaque communication. Cette désinscription ne
            remettra pas en cause votre accès à la base de données. En revanche, si vous décidez de supprimer votre
            compte vous ne recevrez plus d’email et ne serez plus en mesure de voir les offres.

        </p>

        <h2 class="text-2xl font-semibold mb-4">Conformité au RGPD</h2>
        <p class="mb-4">
            Conformément au Règlement (UE) 2016/679 du Parlement européen et du Conseil du 27 avril 2016 relatif à la
            protection des personnes physiques à l’égard du traitement des données à caractère personnel et à la libre
            circulation de ces données : le Règlement général sur la protection des données, ci-après (RGPD), toutes les
            données relatives aux utilisateurs d’Infofin ne sont détenues que par l’ULB, qui assure les mesures de
            sécurité adéquates afin d’en garantir l’intégrité et la confidentialité. Elles ne sont pas partagées avec
            des tiers. Toutes les personnes accédant à ces données sont tenues au respect de la confidentialité. Les
            données relatives au compte sont conservées jusqu’à la suppression de celui-ci par l’utilisateur . Les
            données d’utilisation d’Infofin sont conservées un an.
        </p>

        <h2 class="text-2xl font-semibold mb-4">Contact et Droits</h2>
        <p class="mb-4">
            Le Délégué à la protection des données de l’Université peut être contacté pour toute question relative à la
            protection des données à caractère personnel ou pour toute demande d’exercice de droits (accès,
            rectification, effacement, portabilité et limitation de traitement), à l’adresse suivante : <a
                href="mailto:rgpd@ulb.be"
                class="text-blue-500 underline">rgpd@ulb.be</a>
            (Avenue F. Roosevelt 50 CP 130 1050 Bruxelles).
        </p>
        <p class="mb-4">
            Si vous estimez que notre réponse ne respecte pas vos droits vous pouvez adresser une plainte à l’Autorité
            de protection des données (<a href="https://www.autoriteprotectiondonnees.be"
                                          class="text-blue-500 underline">Introduire une plainte | Autorité de
                protection des données</a>).
        </p>
        <h2 class="text-2xl font-semibold mb-4">Politique des cookies</h2>
        <div class="mb-4">
            <p class="mb-4">
                Conformément à l’avis de l’Autorité de protection des données en la matière, l’ULB vous informe qu’elle
                utilise des cookies dans le cadre de votre connexion et de votre navigation sur son site institutionnel
                de
                façon à en assurer le fonctionnement optimal et pour des finalités analytiques internes. Certains
                cookies
                permettent un affichage graphique optimal, d’autres permettent qu’une application internet gère
                correctement
                les sessions de travail connectées. Les cookies utilisés sur le site de l’ULB ne contiennent pas de
                données
                permettant une identification personnelle de l'utilisateur et sont utilisés dans le seul but de
                collecter
                des informations relatives à l'utilisation du site. Les cookies ont généralement une date d'expiration.
                Par
                exemple, certains cookies sont automatiquement supprimés lorsque vous fermez votre navigateur (cookies
                de
                session), tandis que d'autres restent sur votre appareil pendant une période plus longue (cookies
                permanents).
            </p>
            <p class="mb-4">
                Les cookies utilisés sur ce site sont les suivants :
            </p>
            <ul class="list-disc list-inside">
                <li class="list-item">
                    <span class="font-bold">Cookies fonctionnels et nécessaires (toujours activés)</span>
                    <p class="ml-6">
                        Ces cookies sont nécessaires pour assurer le bon fonctionnement technique du site
                        et pour assurer une
                        visite normale de celui-ci. Ils sont activés par défaut quand vous accédez au site et ne font
                        l’objet que d’une obligation d’information.
                    </p>
                </li>
            </ul>
            <table class="w-fit text-sm text-left text-gray-500 mt-5">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Nom</th>
                    <th scope="col" class="px-6 py-3">Finalité</th>
                    <th scope="col" class="px-6 py-3">Durée de vie</th>
                </tr>
                </thead>
                <tbody>
                <tr class="bg-white border-b">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">XSRF-TOKEN</th>
                    <td class="px-6 py-4">Protection contre la falsification des requêtes intersites</td>
                    <td class="px-6 py-4">Expire quand la session de navigation se termine</td>
                </tr>
                <tr class="bg-white border-b">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">Uid*</th>
                    <td class="px-6 py-4">Maintien de la session sur le serveur</td>
                    <td class="px-6 py-4">Expire quand la session de navigation se termine</td>
                </tr>
                <tr class="bg-white border-b">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">Infofin_session</th>
                    <td class="px-6 py-4">Maintien de la session en cache</td>
                    <td class="px-6 py-4">Expire quand la session de navigation se termine</td>
                </tr>
                <tr class="bg-white border-b">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">CASAuth</th>
                    <td class="px-6 py-4">Gestion de l'authentification via le CAS QuIdAM et mise en cache de la
                        session
                    </td>
                    <td class="px-6 py-4">Expire quand la session de navigation se termine</td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
@endsection
