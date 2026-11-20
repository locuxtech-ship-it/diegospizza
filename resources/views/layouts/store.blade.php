<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#f47b20">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/images/icon-192x192.png">
    <meta property="og:title" content="Diego's Pizza Alameda - Información">
    <meta property="og:description" content="Pide en nuestro menú digital. Obtén toda la información aquí">
    <meta property="og:image" content="https://diegospizzabq.click/logo.png">
    <meta property="og:url" content="https://diegospizzabq.click">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
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

        /* ===== Barra flotante "Mi pedido" ===== */
        .mi-pedido-bar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 45;
            background: linear-gradient(to right, #dc2626, #ea580c);
            color: white;
            padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.18);
            transform: translateY(110%);
            transition: transform 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        .mi-pedido-bar.visible { transform: translateY(0); }
        .mi-pedido-bar .mp-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            min-width: 0;
        }
        .mi-pedido-bar .mp-num { font-weight: 800; font-size: 14px; white-space: nowrap; }
        .mi-pedido-bar .mp-estado {
            font-size: 12px;
            opacity: 0.92;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mi-pedido-bar .mp-cta {
            background: white;
            color: #dc2626;
            font-weight: 700;
            font-size: 14px;
            padding: 9px 18px;
            border-radius: 999px;
            text-decoration: none;
            white-space: nowrap;
            transition: transform 0.15s;
        }
        .mi-pedido-bar .mp-cta:active { transform: scale(0.96); }
        .mi-pedido-bar.resena .mp-cta { color: #ea580c; }
        body.con-barra .cart-fab { bottom: 88px; }
        @media (max-width: 640px) {
            body.con-barra .cart-fab { bottom: 80px; }
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

    {{-- Barra flotante "Mi pedido": recuerda el último pedido del dispositivo --}}
    <div class="mi-pedido-bar" id="mi-pedido-bar" hidden>
        <div class="mp-info">
            <span class="mp-num" id="mp-num"></span>
            <span class="mp-estado" id="mp-estado"></span>
        </div>
        <a href="#" class="mp-cta" id="mp-cta">Ver →</a>
    </div>

    <script>
        (function () {
            var bar = document.getElementById('mi-pedido-bar');
            var numEl = document.getElementById('mp-num');
            var estadoEl = document.getElementById('mp-estado');
            var cta = document.getElementById('mp-cta');
            var ESTADOS = {
                pendiente_pago: '⏳ Pendiente de pago',
                pendiente: '⏳ Pendiente',
                en_proceso: '👨‍🍳 En preparación',
                en_camino: '🚗 En camino',
                entregado: '📍 Entregado',
                finalizado: '🎉 Finalizado',
                cancelado: '❌ Cancelado'
            };

            function ocultar() {
                bar.classList.remove('visible');
                document.body.classList.remove('con-barra');
                setTimeout(function () { bar.hidden = true; }, 300);
            }

            function mostrar(info) {
                bar.hidden = false;
                document.body.classList.add('con-barra');
                requestAnimationFrame(function () { bar.classList.add('visible'); });
                numEl.textContent = '📍 Mi pedido #' + info.numero;
            }

            var raw = localStorage.getItem('miPedidoSeguimiento');
            if (!raw) return;
            var info;
            try { info = JSON.parse(raw); } catch (e) { return; }
            if (!info || !info.token || !info.url) return;

            // Pintar de inmediato con los datos guardados (estado "en vivo" al cargar)
            mostrar(info);
            estadoEl.textContent = ESTADOS[info.estado] || '👁 Seguimiento en vivo';
            cta.textContent = 'Ver →';
            cta.href = info.url;
            bar.classList.remove('resena');

            fetch('/api/seguimiento/' + encodeURIComponent(info.token), { headers: { 'Accept': 'application/json' } })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (data) {
                    // Refresca el estado guardado (la barra queda al día aunque
                    // el cliente no abra la página de seguimiento)
                    info.estado = data.estado;
                    info.numero = data.numero_pedido;
                    localStorage.setItem('miPedidoSeguimiento', JSON.stringify(info));

                    if (data.cancelado) { ocultar(); return; }

                    numEl.textContent = '📍 Mi pedido #' + data.numero_pedido;
                    estadoEl.textContent = ESTADOS[data.estado] || data.estado;

                    if (data.finalizado && data.review_url) {
                        bar.classList.add('resena');
                        estadoEl.textContent = '¡Gracias por tu pedido!';
                        cta.textContent = '⭐ Deja tu reseña';
                        cta.href = data.review_url;
                    } else {
                        bar.classList.remove('resena');
                        cta.textContent = 'Ver →';
                        cta.href = info.url;
                    }
                })
                .catch(function () {
                    // Sin conexión: se queda con el último estado conocido
                });
        })();
    </script>

    @livewireScripts
</body>
</html>