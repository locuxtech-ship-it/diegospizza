@php
$ticketSize = ($negocio->ticket_size ?? '80') === '57' ? '57' : '80';
$escala = max(50, min(300, ($negocio->ticket_escala ?? 100))) / 100;
$fuente = ($negocio->ticket_fuente ?? 'courier') === 'arial' ? 'Arial, Helvetica, sans-serif' : "'Courier New', Courier, monospace";
$lineHeight = match($negocio->ticket_interlineado ?? 'normal') { 'compacto' => 1.0, 'espaciado' => 1.6, default => 1.3 };
$margen = floatval($negocio->ticket_margen ?? 0.8);

if ($ticketSize === '57') {
    $fsBase = round(7 * $escala);
    $fsH1 = round(10 * $escala);
    $fsSub = round(7 * $escala);
    $fsLabel = round(8 * $escala);
    $fsTotal = round(9 * $escala);
    $paperMaxWidth = 48;
    $pageMarginMm = 4.5;
} else {
    $fsBase = round(9 * $escala);
    $fsH1 = round(14 * $escala);
    $fsSub = round(9 * $escala);
    $fsLabel = round(10 * $escala);
    $fsTotal = round(12 * $escala);
    $paperMaxWidth = 80;
    $pageMarginMm = 0;
}
@endphp<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cierre de Caja</title>
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
        .header h1 { font-size: {{ $fsH1 }}px; }
        .header p { font-size: {{ $fsSub }}px; }
        .divider { border-top: 1px dashed #000; margin: 3px 0; }
        .section-title { font-weight: bold; font-size: {{ $fsLabel }}px; margin-bottom: 1px; margin-top: 1px; }
        .row { display: flex; justify-content: space-between; font-size: {{ $fsBase }}px; }
        .total-row { font-size: {{ $fsTotal }}px; font-weight: bold; border-top: 1px solid #000; padding-top: 2px; margin-top: 2px; }
        .gasto-item { display: flex; justify-content: space-between; font-size: {{ $fsBase }}px; padding-left: 4px; }
        .footer { text-align: center; margin-top: 4px; font-size: {{ $fsSub }}px; }
        .obs { margin-top: 2px; font-size: {{ $fsSub }}px; font-style: italic; }
    </style>
</head>
<body>
    <div class="page-content">
        <div class="header">
            <h1>{{ $negocio->nombre_negocio ?? "Diego's Pizza" }}</h1>
            <p>CIERRE DE CAJA</p>
            <p>{{ $cierre->fecha->format('d/m/Y') }}</p>
        </div>

        <div class="divider"></div>

        <div class="section-title">VENTAS DEL DIA</div>
        <div class="row"><span>Efectivo</span><span>${{ number_format($cierre->total_efectivo, 0, ',', '.') }}</span></div>
        @if($esAdmin)
        <div class="row"><span>Transferencia</span><span>${{ number_format($cierre->total_transferencias, 0, ',', '.') }}</span></div>
        <div class="row"><span>Tarjeta</span><span>${{ number_format($cierre->total_tarjeta, 0, ',', '.') }}</span></div>
        <div class="row total-row"><span>TOTAL VENTAS</span><span>${{ number_format($cierre->total_ventas, 0, ',', '.') }}</span></div>
        @endif

        <div class="divider"></div>

        <div class="section-title">GASTOS</div>
        @if($cierre->gastos->isNotEmpty())
            @foreach($cierre->gastos as $gasto)
                <div class="gasto-item">
                    <span>{{ $gasto->descripcion }}</span>
                    <span>-${{ number_format($gasto->monto, 0, ',', '.') }}</span>
                </div>
            @endforeach
        @else
            <div style="font-size: {{ $fsSub }}px;">Sin gastos</div>
        @endif
        <div class="row" style="font-weight: bold;"><span>Total Gastos</span><span>-${{ number_format($cierre->total_gastos, 0, ',', '.') }}</span></div>

        <div class="divider"></div>

        <div class="section-title">CUADRE DE EFECTIVO</div>
        <div class="row"><span>Ventas Efectivo</span><span>${{ number_format($cierre->total_efectivo, 0, ',', '.') }}</span></div>
        <div class="row"><span>Gastos</span><span>-${{ number_format($cierre->total_gastos, 0, ',', '.') }}</span></div>
        <div class="row"><span>Esperado</span><span>${{ number_format($cierre->efectivo_esperado, 0, ',', '.') }}</span></div>
        @if($cierre->efectivo_real !== null)
            @php $diff = $cierre->efectivo_real - $cierre->efectivo_esperado; @endphp
            <div class="row"><span>Real</span><span>${{ number_format($cierre->efectivo_real, 0, ',', '.') }}</span></div>
            <div class="row total-row"><span>Diferencia</span><span>{{ $diff >= 0 ? '+' : '' }}${{ number_format($diff, 0, ',', '.') }}</span></div>
        @endif

        <div class="divider"></div>

        <div style="font-size: {{ $fsSub }}px;">
            <div class="row"><span>Cierre por:</span><span>{{ $cierre->user->name ?? 'N/A' }}</span></div>
            <div class="row"><span>Estado:</span><span>{{ ucfirst($cierre->estado) }}</span></div>
        </div>
        @if($cierre->observaciones)
            <div class="obs">Obs: {{ $cierre->observaciones }}</div>
        @endif

        <div class="divider"></div>

        <div class="footer">
            <p>{{ $negocio->nombre_negocio ?? "Diego's Pizza" }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 1000);
        };
        window.onafterprint = function() {
            setTimeout(function() { window.close(); }, 300);
        };
    </script>
</body>
</html>
