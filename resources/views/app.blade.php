<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="The Magnificent 7 (TM7) Backgammon Club. Manage online backgammon tournaments, team sign-ups, and track solo brackets across the world.">
        <meta name="keywords" content="backgammon, online club, tournaments, brackets, tm7, backgammon leagues, board games">
        <meta name="author" content="Utsav Karki">
        
        <!-- Open Graph -->
        <meta property="og:title" content="TM7 Backgammon Club">
        <meta property="og:description" content="The premier online backgammon club for global players.">
        <meta property="og:type" content="website">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        <link rel="icon" type="image/png" href="/images/logo.png">
        <link rel="shortcut icon" type="image/png" href="/images/logo.png">

        <style>
            @media (min-width: 1024px) {
                html {
                    zoom: 80%;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-300 bg-tm7-darker selection:bg-tm7-gold selection:text-tm7-darker">
        @inertia
    </body>
</html>
