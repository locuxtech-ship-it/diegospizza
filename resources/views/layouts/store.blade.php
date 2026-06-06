<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? "Diego's Pizza" }} - Diego's Pizza</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }

        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        body { font-family: 'Poppins', sans-serif; }

        .cart-slideover {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            max-width: 400px;
            background: white;
            box-shadow: -4px 0 20px rgba(0,0,0,0.1);
            z-index: 60;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        .cart-slideover.open {
            transform: translateX(0);
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 50;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .cart-fab {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 40;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #FF8D08;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(255,141,8,0.4);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .cart-fab:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255,141,8,0.5);
        }
        .cart-fab-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #111827;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        @media (max-width: 640px) {
            .cart-slideover {
                max-width: 100%;
            }
            .cart-fab {
                bottom: 16px;
                right: 16px;
                width: 54px;
                height: 54px;
            }
            .cart-fab-badge {
                width: 20px;
                height: 20px;
                font-size: 10px;
                top: -3px;
                right: -3px;
            }
        }
        @media (min-width: 641px) {
            .cart-fab {
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .cart-fab:hover {
                transform: scale(1.05);
                box-shadow: 0 6px 20px rgba(255,141,8,0.5);
            }
        }

        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
        .animate-slide-up {
            animation: slideUp 0.3s ease-out;
        }
        @media (min-width: 640px) {
            @keyframes fadeScale {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }
            .animate-slide-up {
                animation: fadeScale 0.2s ease-out;
            }
        }
    </style>
</head>
<body class="bg-white font-sans antialiased">
    <div class="min-h-screen bg-white">
        <main>
            {{ $slot }}
        </main>
    </div>

    <livewire:cart />

    @livewireScripts
</body>
</html>