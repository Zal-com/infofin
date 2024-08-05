<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            color: #ffffff;
            background-color: #333333;
        }

        .container {
            display: inline-flex;
            min-width: 800px;
            max-width: 800px;
            padding: var(--L, 24px) 100px;
            flex-direction: column;
            align-items: center;
            gap: var(--L, 24px);
            background: var(--Light-Grey, #F4F5F6);
        }

        .header {
            display: flex;
            align-items: center;
        }

        .content {
            display: flex;
            max-width: 600px;
            padding: var(--L, 24px);
            flex-direction: column;
            align-items: center;
            gap: 16px;
            border-radius: var(--Main-Border-Radius, 16px);
            border: 1px solid var(--Grey, #EAEBED);
            background: var(--White, #FFF);
        }

        .content-text {
            display: flex;
            width: 552px;
            align-items: flex-start;
        }

        .cards-container {
            display: flex;
            width: 561px;
            align-items: flex-start;
            align-content: flex-start;
            gap: 10px;
            flex-wrap: wrap;
            border-radius: 5px;
        }

        .card {
            display: flex;
            width: 275px;
            height: 216px;
            min-width: 200px;
            max-width: 275px;
            padding: var(--S, 8px);
            flex-direction: column;
            align-items: flex-start;
            gap: var(--S, 8px);
            flex-shrink: 0;
            border-radius: 5px;
            border: 2px solid #276D20;
        }

        .card h2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            align-self: stretch;
            overflow: hidden;
            color: var(--ULB, #004C93);
            font-feature-settings: 'liga' off, 'clig' off;
            text-overflow: ellipsis;
            font-family: "Fira Sans";
            font-size: 16px;
            font-style: normal;
            font-weight: 700;
            line-height: 20px; /* 125% */
        }

        .organisation {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            flex: 1 0 0;
            overflow: hidden;
            color: #153654;
            font-feature-settings: 'liga' off, 'clig' off;
            text-overflow: ellipsis;
            font-family: "Fira Sans";
            font-size: 12px;
            font-style: italic;
            font-weight: 400;
            line-height: 17px; /* 141.667% */
        }

        .next-deadline {
            display: flex;
            align-items: flex-start;
            gap: 53px;
            align-self: stretch;
        }

        .next-deadline strong {
            flex: 1 0 0;
            color: var(--Polytech, #000);
            font-family: "Fira Sans";
            font-size: 11px;
            font-style: italic;
            font-weight: 600;
            line-height: normal;
        }

        .next-deadline span {
            color: var(--Polytech, #000);
            font-family: Inter;
            font-size: 11px;
            font-style: italic;
            font-weight: 600;
            line-height: normal;
        }

        .description {
            flex: 1 0 0;
            align-self: stretch;
            overflow: hidden;
            color: var(--Black, #161F33);
            text-align: justify;
            font-feature-settings: 'liga' off, 'clig' off;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-family: "Fira Sans";
            font-size: 12px;
            font-style: normal;
            font-weight: 400;
            line-height: 16px; /* 133.333% */
        }

        .btn-container {
            display: flex;
            width: 275px;
            height: 216px;
            min-width: 200px;
            max-width: 275px;
            padding: var(--S, 8px);
            flex-direction: column;
            align-items: flex-start;
            gap: var(--S, 8px);
            flex-shrink: 0;
            border-radius: 5px;
            border: 2px solid #276D20;
        }

        .button {
            display: flex;
            width: 123px;
            padding: 6px 2px;
            justify-content: space-between;
            align-items: center;
            border-radius: var(--Button-Border-Radius, 4px);
            background: #276D20;
        }

        .footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--L, 24px);
            align-self: stretch;
        }

        .footer-text {
            color: var(--Dark-Grey, #9A9EA6);
            text-align: center;
            font-feature-settings: 'liga' off, 'clig' off;

            /* Footer Text */
            font-family: Helvetica;
            font-size: 12px;
            font-style: normal;
            font-weight: 400;
            line-height: 18px; /* 150% */
        }

        .footer a {
            color: #ffffff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{asset('img/ulb_logo.png')}}" alt="ULB Logo" width="200">
    </div>
    <div class="content">
        <div class="content-text">
            <p>Bonjour {{ $data['prenom'] }},</p>
            <p>Ces appels sont apparus la semaine dernière et pourraient vous intéresser :</p>
        </div>
        <div class="cards-container">
            @foreach ($data['projects'] as $card)
                <div class="card">
                    <h2>{{ $card->title }}</h2>
                    <p class="organisation">{{ $card->organisation }}</p>
                    <div class="next-deadline">
                        <strong>Prochaine deadline :</strong>
                        <span>{{ $card->firstDeadline }}</span></div>
                    <p class="description">{{ $card->short_description }}</p>
                    <div class="btn-container">
                        <a href="{{ url('/projects/' . $card->id) }}" class="button">En savoir plus</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="footer">
            <div class="footer-text">
                <p>Université libre de Bruxelles, 1050 Bruxelles</p>
                <p>Plus envie de recevoir ce mail? <a href="{{$data['url']}}">Se désabonner.</a></p>
            </div>
            {{--
            <div class="social-icons">
                <a href="https://www.facebook.com/ulb"><img src="facebook-icon.png" alt="Facebook"></a>
                <a href="https://www.linkedin.com/school/ulb"><img src="linkedin-icon.png" alt="LinkedIn"></a>
                <a href="https://www.youtube.com/user/ulbvideo"><img src="youtube-icon.png" alt="YouTube"></a>
            </div>
            --}}
        </div>
    </div>
</div>
</body>
</html>

<div
    style="width: 100%; height: 100%; padding-left: 100px; padding-right: 100px; padding-top: 24px; padding-bottom: 24px; background: #F4F5F6; flex-direction: column; justify-content: flex-start; align-items: center; gap: 24px; display: inline-flex">
    <div style="justify-content: flex-start; align-items: center; display: inline-flex">
        <img style="width: 300px; height: 48px; position: relative" src="https://via.placeholder.com/300x48"/>
        <div
            style="width: 300px; text-align: center; color: #004C93; font-size: 32px; font-family: Fira Sans; font-weight: 700; line-height: 18px; word-wrap: break-word">
            Infofin
        </div>
    </div>
    <div
        style="padding: 24px; background: white; border-radius: 16px; border: 1px #EAEBED solid; flex-direction: column; justify-content: flex-start; align-items: center; gap: 16px; display: flex">
        <div style="width: 552px; justify-content: flex-start; align-items: flex-start; display: inline-flex">
            <div
                style="flex: 1 1 0; color: #161F33; font-size: 14px; font-family: Fira Sans; font-weight: 400; line-height: 20px; word-wrap: break-word">
                Bonjour {{$data['prenom']}},<br/><br/>Ces appels sont apparus la semaine dernière et pourraient vous
                intéresser :
            </div>
        </div>
        <div
            style="width: 561px; border-radius: 5px; justify-content: flex-start; align-items: flex-start; gap: 10px; display: inline-flex">
            @foreach($data['projects'] as $card)
                <div
                    style="width: 275px; height: 216px; padding: 8px; border-radius: 5px; border: 2px #276D20 solid; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                    <div
                        style="align-self: stretch; height: 57px; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                        <div
                            style="align-self: stretch; color: #004C93; font-size: 16px; font-family: Fira Sans; font-weight: 700; line-height: 20px; word-wrap: break-word">
                            Financement séjour à l’étranger pour doctorants
                        </div>
                        <div
                            style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                            <div
                                style="flex: 1 1 0; color: #153654; font-size: 12px; font-family: Fira Sans; font-style: italic; font-weight: 400; line-height: 17px; word-wrap: break-word">
                                Commission de classement des crédits internationaux
                            </div>
                            <div
                                style="padding-left: 4px; padding-right: 4px; background: rgba(39, 109, 32, 0.10); border-radius: 4px; justify-content: center; align-items: center; gap: 2px; display: flex">
                                <div
                                    style="color: #276D20; font-size: 10px; font-family: Inter; font-weight: 600; line-height: 16px; word-wrap: break-word">
                                    Financement
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 53px; display: inline-flex">
                        <div
                            style="flex: 1 1 0; color: black; font-size: 11px; font-family: Fira Sans; font-style: italic; font-weight: 600; word-wrap: break-word">
                            Prochaine deadline :
                        </div>
                        <div
                            style="color: black; font-size: 11px; font-family: Inter; font-style: italic; font-weight: 600; word-wrap: break-word">
                            31/08/2024
                        </div>
                    </div>
                    <div
                        style="align-self: stretch; flex: 1 1 0; text-align: justify; color: #161F33; font-size: 12px; font-family: Fira Sans; font-weight: 400; line-height: 16px; word-wrap: break-word">
                        Crédits octroyés aux doctorants de l'ULB dans le cadre de la préparation de leur thèse de
                        doctorat
                        pour participer au financement de séjours, de courte et moyenne durée (2 à 6 mois maximum), au
                        sein
                        d'une université étrangère (crédits CCCI et bourses FWB) ou sur un terrain de recherche (bourses
                        FWB
                        uniquement)
                    </div>
                    <div
                        style="align-self: stretch; height: 29px; flex-direction: column; justify-content: flex-start; align-items: flex-end; gap: 10px; display: flex">
                        <div
                            style="width: 123px; padding-left: 2px; padding-right: 2px; padding-top: 6px; padding-bottom: 6px; background: #276D20; border-radius: 4px; justify-content: space-between; align-items: center; display: inline-flex">
                            <div
                                style="text-align: center; color: white; font-size: 12px; font-family: Fira Sans; font-weight: 400; line-height: 17px; word-wrap: break-word">
                                En savoir plus
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div
                    style="align-self: stretch; height: 108px; flex-direction: column; justify-content: flex-start; align-items: center; gap: 24px; display: flex">
                    <div
                        style="align-self: stretch; justify-content: flex-end; align-items: center; gap: 10px; display: inline-flex">
                        <div
                            style="text-align: center; color: #9A9EA6; font-size: 12px; font-family: Helvetica; font-weight: 400; line-height: 18px; word-wrap: break-word">
                            Université libre de Bruxelles, 1050 Bruxelles <br/>Plus envie de recevoir ce mail? Se
                            désabonner.
                        </div>
                    </div>
                    <div
                        style="align-self: stretch; justify-content: center; align-items: flex-start; gap: 8px; display: inline-flex">
                        <div style="width: 48px; height: 48px; position: relative">
                            <div
                                style="width: 48px; height: 48px; left: 0px; top: 0px; position: absolute; background: white; border-radius: 9999px; border: 2px #9A9EA6 solid"></div>
                            <div
                                style="width: 12.18px; height: 26.11px; left: 18px; top: 11px; position: absolute; background: #9A9EA6"></div>
                        </div>
                        <div style="width: 48px; height: 48px; position: relative">
                            <div
                                style="width: 48px; height: 48px; left: 0px; top: 0px; position: absolute; background: white; border-radius: 9999px; border: 2px #9A9EA6 solid"></div>
                            <img style="width: 25.38px; height: 24.22px; left: 11.20px; top: 12px; position: absolute"
                                 src="https://via.placeholder.com/25x24"/>
                        </div>
                        <div style="width: 48px; height: 48px; position: relative">
                            <div
                                style="width: 48px; height: 48px; left: 0px; top: 0px; position: absolute; background: white; border-radius: 9999px; border: 2px #9A9EA6 solid"></div>
                            <img
                                style="width: 25.60px; height: 18.40px; left: 11.20px; top: 15.20px; position: absolute"
                                src="https://via.placeholder.com/26x18"/>
                        </div>
                    </div>
                </div>
        </div>
