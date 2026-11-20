{{-- Timeline de seguimiento: render inicial del servidor.
     El JS de la página lo reemplaza con cada poll usando el mismo formato. --}}
@if($pedido->estado === 'cancelado')
    <div class="cancelado-box">❌ Pedido cancelado
        <small>Si tienes preguntas, contáctanos por WhatsApp.</small>
    </div>
@else
    <div class="timeline">
        @foreach($pasos as $p)
            <div class="step {{ $p['estado'] }}">
                <div class="rail"><div class="dot">{{ $p['icono'] }}</div><div class="line"></div></div>
                <div class="body">
                    <div class="titulo">{{ $p['titulo'] }}</div>
                    <div class="hora">{{ $p['hora'] }}{{ $p['subtitulo'] ? ' — ' . $p['subtitulo'] : '' }}</div>
                </div>
            </div>
        @endforeach
    </div>
@endif
