<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/d8f60edc6b.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Infofin</title>
    @livewireStyles
</head>
<body class="flex flex-col">
@auth()
    {{\Illuminate\Support\Facades\Auth::getUser()->getAuthIdentifier()}}
@endauth
@include('components.header')

<main class="container w-75 m-auto">
    @yield('content')
    @livewireScripts
</main>
@include('components.footer')
@livewireScripts
</body>
</html>
