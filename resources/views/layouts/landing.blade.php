<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Facilo - La plataforma SaaS para restaurantes. Menú digital, pedidos, pagos y más.">
    <meta name="theme-color" content="#F1590C">
    <meta property="og:title" content="Facilo - SaaS para Restaurantes">
    <meta property="og:description" content="Lleva tu restaurante al siguiente nivel con menú digital, pedidos en línea y reportes.">
    <meta property="og:image" content="{{ asset('icons/facilo-icon.svg') }}">
    <meta property="og:type" content="website">
    <title>Facilo - SaaS para Restaurantes</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Nunito', sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">
            @yield('content')
    @livewireScripts
</body>
</html>
