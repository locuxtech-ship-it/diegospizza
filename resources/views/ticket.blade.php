@php
$ticketSize = ($negocio->ticket_size ?? '80') === '57' ? '57' : '80';
$escala = max(50, min(300, ($negocio->ticket_escala ?? 100))) / 100;
$mostrarLogo = $negocio->ticket_mostrar_logo ?? true;
$negritas = $negocio->ticket_negritas ?? true;
$fuente = ($negocio->ticket_fuente ?? 'courier') === 'arial' ? 'Arial, Helvetica, sans-serif' : "'Courier New', Courier, monospace";
$lineHeight = match($negocio->ticket_interlineado ?? 'normal') { 'compacto' => 1.0, 'espaciado' => 1.6, default => 1.3 };
$espaciadoProd = match($negocio->ticket_espaciado ?? 'normal') { 'compacto' => 0, 'espaciado' => 4, default => 1 };
$margen = floatval($negocio->ticket_margen ?? 0.8);

if ($ticketSize === '57') {
    $fsBase = round(7 * $escala);
    $fsH1 = round(10 * $escala);
    $fsSub = round(6 * $escala);
    $fsRef = round(9 * $escala);
    $fsInfo = round(7 * $escala);
    $fsSection = round(8 * $escala);
    $fsProduct = round(7 * $escala);
    $fsVariant = round(6 * $escala);
    $fsTotal = round(9 * $escala);
    $fsFooter = round(7 * $escala);
    $fsAddress = round(8 * $escala);
    $qtyWidth = round(14 * $escala);
    $priceWidth = round(28 * $escala);
    $paperMaxWidth = 48;
    $pageMarginMm = 4.5;
} else {
    $fsBase = round(9 * $escala);
    $fsH1 = round(14 * $escala);
    $fsSub = round(8 * $escala);
    $fsRef = round(12 * $escala);
    $fsInfo = round(9 * $escala);
    $fsSection = round(10 * $escala);
    $fsProduct = round(9 * $escala);
    $fsVariant = round(7 * $escala);
    $fsTotal = round(12 * $escala);
    $fsFooter = round(9 * $escala);
    $fsAddress = round(10 * $escala);
    $qtyWidth = round(22 * $escala);
    $priceWidth = round(42 * $escala);
    $paperMaxWidth = 80;
    $pageMarginMm = 0;
}
@endphp<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ticket #{{ $pedido->numero_pedido }}</title>
    <style>
        @page { margin: 0 {{ $pageMarginMm }}mm; size: {{ $ticketSize }}mm auto; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: {{ $paperMaxWidth }}mm;
            max-width: {{ $paperMaxWidth }}mm;
            font-family: {{ $fuente }};
            color: #000;
            font-size: {{ $fsBase }}px;
            line-height: {{ $lineHeight }};
        }

        .page-content { padding: {{ $margen }}mm; }

        .header { text-align: center; margin-bottom: 2px; }
        .header h1 { font-size: {{ $fsH1 }}px; font-weight: {{ $negritas ? 'bold' : 'normal' }}; }
        .header p { font-size: {{ $fsSub }}px; margin-top: 0; }
        .divider { border-top: 1px dashed #000; margin: 3px 0; }
        .order-ref { text-align: center; font-size: {{ $fsRef }}px; font-weight: {{ $negritas ? 'bold' : 'normal' }}; margin-bottom: 2px; }
        .info-row { display: flex; justify-content: space-between; font-size: {{ $fsInfo }}px; }
        .info-label { font-weight: {{ $negritas ? 'bold' : 'normal' }}; }
        .address-box { margin: 3px 0; }
        .section-title { font-weight: {{ $negritas ? 'bold' : 'normal' }}; font-size: {{ $fsSection }}px; margin-bottom: 1px; margin-top: 1px; }
        .product-item { padding: {{ $espaciadoProd }}px 0; }
        .product-row { display: flex; align-items: flex-start; font-size: {{ $fsProduct }}px; }
        .product-qty { width: {{ $qtyWidth }}px; text-align: right; flex-shrink: 0; }
        .product-name { flex: 1; padding: 0 2px; word-wrap: break-word; overflow-wrap: break-word; }
        .product-price { width: {{ $priceWidth }}px; text-align: right; flex-shrink: 0; }
        .product-sub { font-size: {{ $fsVariant }}px; padding-left: {{ $qtyWidth }}px; }
        .totals { margin-top: 2px; }
        .total-row { display: flex; justify-content: space-between; font-size: {{ $fsInfo }}px; padding: 0; }
        .grand-total { font-size: {{ $fsTotal }}px; font-weight: {{ $negritas ? 'bold' : 'normal' }}; border-top: 1px solid #000; padding-top: 2px; margin-top: 2px; }
        .payment-info { margin-top: 3px; }
        .payment-info p { font-size: {{ $fsInfo }}px; }
        .footer { text-align: center; margin-top: 4px; font-size: {{ $fsFooter }}px; }
        .notas { margin-top: 2px; font-size: {{ $fsInfo }}px; font-style: italic; }
        .logo-img { max-width: {{ $ticketSize === '57' ? '45' : '65' }}mm; max-height: {{ $ticketSize === '57' ? '15' : '22' }}mm; margin-bottom: 2px; }
    </style>
</head>
<body>
    <div class="page-content">
        <div class="header">
            @if($mostrarLogo && $negocio->logo)
                <img src="{{ asset('storage/'.$negocio->logo) }}" alt="Logo" class="logo-img">
            @endif
            <h1>{{ $negocio->nombre_negocio ?? "Diego's Pizza" }}</h1>
            <p>{{ $negocio->direccion ?? '' }}</p>
        </div>

        <div class="divider"></div>

        <div class="order-ref">#{{ $pedido->numero_pedido }}</div>
        <div class="info-row">
            <span>{{ $pedido->created_at->setTimezone('America/Bogota')->format('d/m/Y H:i') }}</span>
            <span>{{ ucfirst($pedido->metodo_pago) }}</span>
        </div>
        <div class="info-row" style="margin-top: 1px;">
            <span class="info-label">Tel:</span>
            <span>{{ $pedido->cliente->telefono }}</span>
        </div>

        <div class="divider"></div>

        <div class="section-title">📍 DIRECCION</div>
        <div class="address-box">
            <div style="font-size: {{ $fsInfo }}px;">{{ $pedido->cliente->nombre }}</div>
            <div style="font-size: {{ $fsInfo }}px; margin-top: 1px;">Conjunto: {{ $pedido->direccion_conjunto }}</div>
            @if($pedido->direccion_torre)
                <div style="font-size: {{ $fsInfo }}px;">Torre: {{ $pedido->direccion_torre }}</div>
            @endif
            @if($pedido->direccion_apto)
                <div style="font-size: {{ $fsInfo }}px;">Apto: {{ $pedido->direccion_apto }}</div>
            @endif
            <div style="font-size: {{ $fsInfo }}px;">Tel: {{ $pedido->cliente->telefono }}</div>
        </div>

        @if($pedido->notas)
        <div class="notas">
            <span class="info-label">📌</span> {{ $pedido->notas }}
        </div>
        @endif

        <div class="divider"></div>

        <div class="section-title">Productos</div>
        @foreach($productos as $pp)
            <div class="product-item">
                <div class="product-row">
                    <span class="product-qty">{{ $pp->cantidad }}x</span>
                    <span class="product-name">{{ $pp->producto->nombre }}@if($pp->mitades) [{{ collect($pp->mitades)->pluck('nombre')->implode(' / ') }}]@endif</span>
                    <span class="product-price">${{ number_format($pp->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($pp->variant_tamanio)
                    <div class="product-sub">({{ $pp->variant_tamanio }})</div>
                @endif
            </div>
        @endforeach

        <div class="divider"></div>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>${{ number_format($pedido->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($pedido->descuento_puntos > 0)
            <div class="total-row">
                <span>Desc. puntos</span>
                <span>-${{ number_format($pedido->descuento_puntos, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($pedido->descuento_manual > 0)
            <div class="total-row">
                <span>Desc. manual</span>
                <span>-${{ number_format($pedido->descuento_manual, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span>${{ number_format($pedido->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="payment-info">
            <p><span class="info-label">{{ ucfirst($pedido->metodo_pago) }}</span></p>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>¡Gracias por tu pedido!</p>
            <p>{{ $negocio->nombre_negocio ?? "Diego's Pizza" }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            if (window === window.top) {
                setTimeout(function() { window.print(); }, 1000);
            }
        };
        window.onafterprint = function() {
            if (window === window.top) {
                setTimeout(function() { window.close(); }, 300);
            }
        };
    </script>
</body>
</html>
