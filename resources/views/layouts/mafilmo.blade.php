<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaFilmo — @yield('title', 'Votre journal de cinéma')</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- CSS Global MaFilmo --}}
    <link rel="stylesheet" href="{{ asset('css/mafilmo.css') }}">

    {{-- Token CSRF pour les requêtes AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body class="@yield('body-class', 'page-bg')">

    {{-- Contenu de la page --}}
    @yield('content')

    {{-- JS Global MaFilmo --}}
    <script src="{{ asset('js/mafilmo.js') }}" defer></script>

</body>
</html>

