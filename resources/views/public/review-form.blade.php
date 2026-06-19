<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deja tu reseña - Diego's Pizza</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
            padding: 40px 32px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        .logo { font-size: 28px; font-weight: 900; color: #dc2626; margin-bottom: 4px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 24px; }
        .pedido-info {
            background: #f9fafb;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #374151;
        }
        .stars {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 24px;
            flex-direction: row-reverse;
        }
        .stars input { display: none; }
        .stars label {
            font-size: 42px;
            cursor: pointer;
            color: #d1d5db;
            transition: color 0.15s;
        }
        .stars label:hover,
        .stars label:hover ~ label,
        .stars input:checked ~ label {
            color: #f59e0b;
        }
        textarea {
            width: 100%;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 15px;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 20px;
            outline: none;
            transition: border-color 0.15s;
        }
        textarea:focus { border-color: #dc2626; }
        .btn {
            width: 100%;
            background: linear-gradient(to right, #dc2626, #ef4444);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.9; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .msg {
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .msg-success { background: #d1fae5; color: #065f46; }
        .msg-error { background: #fef2f2; color: #991b1b; }
        .already { color: #6b7280; font-size: 14px; padding: 20px 0; }
        .already strong { color: #f59e0b; font-size: 28px; display: block; margin-bottom: 8px; }
        .rating-text { font-size: 13px; color: #9ca3af; margin-bottom: 20px; margin-top: -16px; }
        @media (max-width: 480px) {
            .card { padding: 28px 20px; }
            .stars label { font-size: 36px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Diego's Pizza</div>
        <div class="subtitle">¿Cómo fue tu experiencia?</div>

        @if(session('success'))
            <div class="msg msg-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="msg msg-error">{{ session('error') }}</div>
        @endif

        @if($review)
            <div class="already">
                <strong>
                    @for($i = 1; $i <= $review->rating; $i++)⭐@endfor
                </strong>
                Ya calificaste este pedido con {{ $review->rating }} estrella(s).
                @if($review->comentario)
                    <br><br><em>"{{ $review->comentario }}"</em>
                @endif
            </div>
        @else
            <div class="pedido-info">
                Pedido #{{ $pedido->numero_pedido }} — {{ $pedido->cliente?->nombre ?? 'Cliente' }}
            </div>

            <form method="POST" action="{{ url('/review/' . $pedido->numero_pedido) }}" id="reviewForm">
                @csrf
                <div class="stars">
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" required>
                        <label for="star{{ $i }}">★</label>
                    @endfor
                </div>
                <div class="rating-text">Seleccioná la cantidad de estrellas</div>

                <textarea name="comentario" placeholder="Contanos cómo fue tu experiencia (opcional)..." maxlength="1000"></textarea>

                @error('rating') <div class="msg msg-error">{{ $message }}</div> @enderror
                @error('comentario') <div class="msg msg-error">{{ $message }}</div> @enderror

                <button type="submit" class="btn">Enviar Reseña</button>
            </form>
        @endif

        <div style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
            <a href="{{ url('/') }}" style="color: #dc2626; text-decoration: none;">← Volver al menú</a>
        </div>
    </div>

    <script>
        document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn');
            btn.disabled = true;
            btn.textContent = 'Enviando...';
        });
    </script>
</body>
</html>
