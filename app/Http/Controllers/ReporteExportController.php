<?php

namespace App\Http\Controllers;

use App\Services\ReporteService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteExportController extends Controller
{
    public function exportar(Request $request): StreamedResponse
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $periodo = (string) $request->query('periodo', 'hoy');
        $fechaInicio = $request->query('fechaInicio');
        $fechaFin = $request->query('fechaFin');

        [$desde, $hasta] = ReporteService::rango($periodo, $fechaInicio, $fechaFin);

        $pedidos = ReporteService::pedidosQuery($desde, $hasta)
            ->with('cliente')
            ->select('id', 'numero_pedido', 'cliente_id', 'created_at', 'subtotal', 'descuento_puntos', 'descuento_manual', 'total', 'origen', 'estado')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalVentas = (float) $pedidos->sum('total');
        $totalPedidos = $pedidos->count();
        $promedioPedido = $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0;

        $comparativa = ReporteService::comparativa();
        $desglose = ReporteService::desgloseMensual(12);
        $fases = ReporteService::tiemposFases($desde, $hasta);
        $repartidores = ReporteService::metricasRepartidores($desde, $hasta);

        $nombreArchivo = 'reporte_ventas'
            . ($desde ? '_' . $desde->format('Y-m-d') : '')
            . ($hasta ? '_al_' . $hasta->format('Y-m-d') : '')
            . '.csv';

        $callback = function () use ($pedidos, $totalVentas, $totalPedidos, $promedioPedido, $comparativa, $desglose, $fases, $repartidores) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM UTF-8 (Excel)

            fputcsv($file, ['Pedido', 'Fecha', 'Hora', 'Cliente', 'Subtotal', 'Dcto Puntos', 'Dcto Manual', 'Total', 'Origen', 'Estado'], ';');

            foreach ($pedidos as $p) {
                fputcsv($file, [
                    $p->numero_pedido,
                    $p->created_at->format('d/m/Y'),
                    $p->created_at->format('H:i'),
                    $p->cliente->nombre ?? 'N/A',
                    number_format($p->subtotal, 0, ',', '.'),
                    number_format($p->descuento_puntos, 0, ',', '.'),
                    number_format($p->descuento_manual, 0, ',', '.'),
                    number_format($p->total, 0, ',', '.'),
                    $p->origen,
                    $p->estado,
                ], ';');
            }

            fputcsv($file, [], ';');
            fputcsv($file, [], ';');
            fputcsv($file, ['RESUMEN'], ';');
            fputcsv($file, ['Total Ventas', '$' . number_format($totalVentas, 0, ',', '.')], ';');
            fputcsv($file, ['Total Pedidos', $totalPedidos], ';');
            fputcsv($file, ['Promedio por Pedido', '$' . number_format($promedioPedido, 0, ',', '.')], ';');

            fputcsv($file, [], ';');
            fputcsv($file, ['COMPARATIVA'], ';');
            fputcsv($file, ['Semana Actual', '$' . number_format($comparativa['semanaActual'], 0, ',', '.')], ';');
            fputcsv($file, ['Semana Anterior', '$' . number_format($comparativa['semanaAnterior'], 0, ',', '.')], ';');
            fputcsv($file, ['Variacion Semanal', self::porcentaje($comparativa['porcentajeSemanal'])], ';');
            fputcsv($file, ['Mes Actual', '$' . number_format($comparativa['mesActual'], 0, ',', '.')], ';');
            fputcsv($file, ['Mes Anterior', '$' . number_format($comparativa['mesAnterior'], 0, ',', '.')], ';');
            fputcsv($file, ['Variacion Mensual', self::porcentaje($comparativa['porcentajeMensual'])], ';');
            fputcsv($file, ['Acumulado Anual', '$' . number_format($comparativa['acumuladoAnual'], 0, ',', '.')], ';');
            fputcsv($file, ['Promedio Mensual', '$' . number_format($comparativa['promedioMensualAnual'], 0, ',', '.')], ';');

            fputcsv($file, [], ';');
            fputcsv($file, ['TIEMPOS DE ENTREGA POR FASES'], ';');
            fputcsv($file, ['Fase', 'Pedidos', 'Promedio', 'Mediana'], ';');

            $etiquetasFases = [
                'preparacion' => 'Preparacion (pedido a salir a ruta)',
                'ruta' => 'En ruta (salir a llegar)',
                'total' => 'Total (pedido a llegar)',
            ];
            foreach ($etiquetasFases as $clave => $etiqueta) {
                $f = $fases[$clave];
                fputcsv($file, [
                    $etiqueta,
                    $f['pedidos'],
                    $f['pedidos'] > 0 ? $f['promedio'] . ' min' : '-',
                    $f['pedidos'] > 0 ? $f['mediana'] . ' min' : '-',
                ], ';');
            }

            fputcsv($file, [], ';');
            fputcsv($file, ['METRICAS POR REPARTIDOR'], ';');
            fputcsv($file, ['Repartidor', 'Entregas', 'Prom. en ruta', 'Prom. total', 'Prom. en puerta'], ';');

            foreach ($repartidores as $m) {
                fputcsv($file, [
                    $m['repartidor'],
                    $m['entregas'],
                    $m['ruta']['pedidos'] > 0 ? $m['ruta']['promedio'] . ' min' : '-',
                    $m['total']['pedidos'] > 0 ? $m['total']['promedio'] . ' min' : '-',
                    $m['puerta']['pedidos'] > 0 ? $m['puerta']['promedio'] . ' min' : '-',
                ], ';');
            }

            fputcsv($file, [], ';');
            fputcsv($file, ['DESGLOSE MES A MES (ultimos 12 meses)'], ';');
            fputcsv($file, ['Mes', 'Pedidos', 'Ventas', 'Promedio por Pedido', 'Variacion vs Mes Anterior'], ';');

            $totalMesesPedidos = 0;
            $totalMesesVentas = 0.0;
            foreach ($desglose as $mes) {
                $totalMesesPedidos += $mes['pedidos'];
                $totalMesesVentas += $mes['ventas'];

                fputcsv($file, [
                    $mes['label'] . ($mes['actual'] ? ' (en curso)' : ''),
                    $mes['pedidos'],
                    '$' . number_format($mes['ventas'], 0, ',', '.'),
                    '$' . number_format($mes['promedio'], 0, ',', '.'),
                    $mes['variacion'] === null ? '-' : self::porcentaje($mes['variacion']),
                ], ';');
            }

            fputcsv($file, [
                'TOTAL',
                $totalMesesPedidos,
                '$' . number_format($totalMesesVentas, 0, ',', '.'),
                $totalMesesPedidos > 0 ? '$' . number_format($totalMesesVentas / $totalMesesPedidos, 0, ',', '.') : '$0',
                '',
            ], ';');

            fclose($file);
        };

        return response()->streamDownload($callback, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private static function porcentaje(float $valor): string
    {
        return ($valor >= 0 ? '+' : '') . number_format($valor, 1, ',', '.') . '%';
    }
}
