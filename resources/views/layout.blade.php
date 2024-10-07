<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og:title', 'Infofin')"/>
    <meta property="og:description" content="@yield('og:description', 'Description Infofin')"/>
    <meta property="og:image" content="@yield('og:image', asset('default-image.jpg'))"/>
    <meta property="og:url" content="@yield('og:url', url()->current())"/>
    <meta property="og:type" content="@yield('og:type', 'website')"/>

    <!-- Other meta tags, styles, etc. -->
    <link rel="icon" href="{{ url('img/favicon.svg') }}">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/d8f60edc6b.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Infofin</title>
    @filamentStyles
</head>
<body class="flex flex-col min-h-screen overflow-y-auto  [&::-webkit-scrollbar]:w-2
  [&::-webkit-scrollbar-track]:rounded-full
  [&::-webkit-scrollbar-track]:bg-gray-100
  [&::-webkit-scrollbar-thumb]:rounded-full
  [&::-webkit-scrollbar-thumb]:bg-gray-300
  dark:[&::-webkit-scrollbar-track]:bg-neutral-700
  dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
@include('components.header')

<main class="container w-75 m-auto flex-1">
    @livewire('notifications')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error-layout'))
        <div class="mt-4 p-4 bg-red-500 text-white rounded-2xl mb-4">{{ session('error-layout') }}</div>
    @endif
    @yield('content')
    @filamentScripts
</main>
@include('components.footer')
</body>
</html>
