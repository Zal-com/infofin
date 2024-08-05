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
                <p>Plus envie de recevoir ce mail? <a href="#">Se désabonner.</a></p>
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
