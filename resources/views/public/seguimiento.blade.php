<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex, nofollow">
    <title>Seguimiento de pedido #{{ $pedido->numero_pedido }} - Diego's Pizza</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 36px 32px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        .logo { font-size: 28px; font-weight: 900; color: #dc2626; margin-bottom: 4px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 20px; }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
            color: #16a34a;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 999px;
            margin-bottom: 20px;
        }
        .live-badge.offline { background: #f3f4f6; color: #9ca3af; }
        .live-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #16a34a;
            animation: pulse 1.5s infinite;
        }
        .live-badge.offline .live-dot { background: #9ca3af; animation: none; }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        .pedido-head {
            background: #f9fafb;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pedido-head .num { font-size: 22px; font-weight: 900; color: #111827; }
        .pedido-head .fecha { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .pedido-head .total { font-size: 18px; font-weight: 800; color: #dc2626; }

        /* ===== TIMELINE ===== */
        .timeline { text-align: left; margin-bottom: 8px; position: relative; }
        .step {
            display: flex;
            gap: 14px;
            position: relative;
            padding-bottom: 22px;
        }
        .step:last-child { padding-bottom: 0; }
        .step .rail {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .step .dot {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            background: #e5e7eb;
            color: #9ca3af;
            flex-shrink: 0;
            z-index: 1;
            transition: all 0.3s;
        }
        .step .line {
            width: 3px;
            flex: 1;
            background: #e5e7eb;
            margin-top: 4px;
            min-height: 22px;
        }
        .step:last-child .line { display: none; }

        .step.done .dot { background: #dc2626; color: white; }
        .step.done .line { background: #dc2626; }

        .step.current .dot {
            background: #16a34a;
            color: white;
            box-shadow: 0 0 0 6px rgba(22, 163, 74, 0.18);
            animation: pulse 1.5s infinite;
        }

        .step .body { padding-top: 8px; flex: 1; }
        .step .titulo { font-size: 15px; font-weight: 700; color: #111827; }
        .step .hora { font-size: 12px; color: #9ca3af; margin-top: 2px; }
        .step.pending .titulo { color: #9ca3af; font-weight: 600; }
        .step.current .titulo { color: #16a34a; }

        /* Estado cancelado */
        .cancelado-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 12px;
            padding: 16px;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: left;
        }
        .cancelado-box small { display: block; font-weight: 500; font-size: 12px; margin-top: 4px; color: #dc2626; }

        /* Productos */
        .productos {
            border-top: 1px solid #f3f4f6;
            margin-top: 22px;
            padding-top: 16px;
            text-align: left;
        }
        .productos h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin-bottom: 10px;
        }
        .prod-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #374151;
            padding: 5px 0;
        }
        .prod-row .cant { font-weight: 700; color: #111827; margin-right: 6px; }

        .btn-review {
            display: block;
            width: 100%;
            margin-top: 20px;
            background: linear-gradient(to right, #f59e0b, #f97316);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .btn-menu {
            display: inline-block;
            margin-top: 16px;
            color: #dc2626;
            font-size: 13px;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .card { padding: 26px 20px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Diego's Pizza</div>
        <div class="subtitle">Seguimiento de tu pedido</div>

        <div class="live-badge" id="live-badge"><span class="live-dot"></span> ACTUALIZANDO EN VIVO</div>

        <div class="pedido-head">
            <div style="text-align:left;">
                <div class="num">#{{ $pedido->numero_pedido }}</div>
                <div class="fecha">{{ $pedido->created_at->copy()->setTimezone('America/Bogota')->format('d/m/Y · g:i a') }}</div>
            </div>
            <div class="total">${{ number_format((float) $pedido->total, 0, ',', '.') }}</div>
        </div>

        {{-- Timeline / aviso de cancelación (se re-renderiza con cada poll) --}}
        <div id="seg-estado">
            @include('public.seguimiento-estado', ['pasos' => $pasos, 'pedido' => $pedido])
        </div>

        {{-- Productos (estáticos: no cambian) --}}
        <div class="productos">
            <h4>Tu pedido</h4>
            @foreach($productos as $pp)
                <div class="prod-row">
                    <span>
                        <span class="cant">{{ $pp->cantidad }}x</span>
                        {{ $pp->producto->nombre }}@if($pp->mitades) [{{ collect($pp->mitades)->pluck('nombre')->implode(' / ') }}]@endif@if($pp->variant_tamanio) ({{ $pp->variant_tamanio }})@endif
                    </span>
                    <span>${{ number_format((float) $pp->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        <div id="seg-review">
            @if($pedido->estado === 'finalizado')
                <a href="{{ route('review.form', ['pedido' => $pedido->id]) }}" class="btn-review">⭐ Deja tu reseña</a>
            @endif
        </div>

        <a href="{{ route('menu') }}" class="btn-menu">← Volver al menú</a>
    </div>

    <script>
        (function () {
            var token = @json($token);
            var endpoint = @json(route('seguimiento.status', ['token' => $token]));
            var badge = document.getElementById('live-badge');
            var estadoBox = document.getElementById('seg-estado');
            var reviewBox = document.getElementById('seg-review');

            function render(data) {
                if (data.cancelado) {
                    estadoBox.innerHTML =
                        '<div class="cancelado-box">❌ Pedido cancelado' +
                        '<small>Si tienes preguntas, contáctanos por WhatsApp.</small></div>';
                    reviewBox.innerHTML = '';
                    return;
                }

                var html = '<div class="timeline">';
                data.pasos.forEach(function (p) {
                    var sub = p.subtitulo ? ' — ' + p.subtitulo : '';
                    html +=
                        '<div class="step ' + p.estado + '">' +
                            '<div class="rail"><div class="dot">' + p.icono + '</div><div class="line"></div></div>' +
                            '<div class="body">' +
                                '<div class="titulo">' + p.titulo + '</div>' +
                                '<div class="hora">' + (p.hora || '') + sub + '</div>' +
                            '</div>' +
                        '</div>';
                });
                html += '</div>';
                estadoBox.innerHTML = html;

                reviewBox.innerHTML = data.finalizado && data.review_url
                    ? '<a href="' + data.review_url + '" class="btn-review">⭐ Deja tu reseña</a>'
                    : '';
            }

            function poll() {
                fetch(endpoint, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) {
                        if (!r.ok) throw new Error(r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        badge.classList.remove('offline');
                        badge.innerHTML = '<span class="live-dot"></span> ACTUALIZANDO EN VIVO';
                        render(data);
                    })
                    .catch(function () {
                        badge.classList.add('offline');
                        badge.innerHTML = '<span class="live-dot"></span> SIN CONEXIÓN — reintentando…';
                    });
            }

            setInterval(poll, 5000);
        })();
    </script>
</body>
</html>
