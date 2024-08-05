<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            color: #333333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .content {
            background-color: #f7f7f7;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 10px;
        }

        .card h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .card .button {
            display: inline-block;
            padding: 10px 20px;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .footer {
            text-align: center;
            font-size: 14px;
        }

        .social-icons {
            text-align: center;
            margin-top: 20px;
        }

        .social-icons a {
            display: inline-block;
            margin: 0 5px;
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
        <p>Bonjour {{ $data['prenom'] }},</p>
        <p>Ces appels sont apparus la semaine dernière et pourraient vous intéresser :</p>
    </div>
    @foreach ($data['projects'] as $card)
        <div class="card" style="background-color: #FFFFFF">
            <h2>{{ $card->title }}</h2>
            <p>{{ $card->organisation }}</p>
            <p><strong>Prochaine deadline :</strong> {{ $card->firstDeadline }}</p>
            <p>{{ $card->short_description }}</p>
            <a href={{ url('/projects/' . $card->id) }} class="button"
               style="background-color: #FFFFFF">En
                savoir plus</a>
        </div>
    @endforeach
    <div class="footer">
        <p>Université libre de Bruxelles, 1050 Bruxelles</p>
        <p>Plus envie de recevoir ce mail? <a href="#">Se désabonner.</a></p>
        {{--
        <div class="social-icons">
            <a href="https://www.facebook.com/ulb"><img src="facebook-icon.png" alt="Facebook"></a>
            <a href="https://www.linkedin.com/school/ulb"><img src="linkedin-icon.png" alt="LinkedIn"></a>
            <a href="https://www.youtube.com/user/ulbvideo"><img src="youtube-icon.png" alt="YouTube"></a>
        </div>
        --}}
    </div>
</div>
</body>
</html>
