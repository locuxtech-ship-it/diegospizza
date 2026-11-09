<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ReporteService
{
    /**
     * Devuelve [desde, hasta] como Carbon|null según el período seleccionado.
     * La semana del negocio va de martes a lunes.
     */
    public static function rango(string $periodo, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $hoy = now();

        return match ($periodo) {
            'ayer' => [$hoy->copy()->subDay()->startOfDay(), $hoy->copy()->subDay()->endOfDay()],
            '7d' => [$hoy->copy()->subDays(7)->startOfDay(), $hoy->copy()->endOfDay()],
            '30d' => [$hoy->copy()->subDays(30)->startOfDay(), $hoy->copy()->endOfDay()],
            'personalizado' => (function () use ($hoy, $fechaInicio, $fechaFin) {
                $desde = self::fechaValida($fechaInicio)?->startOfDay();
                $hasta = self::fechaValida($fechaFin)?->endOfDay();

                if (!$desde && !$hasta) {
                    return [$hoy->copy()->startOfDay(), $hoy->copy()->endOfDay()];
                }

                return [$desde, $hasta];
            })(),
            default => [$hoy->copy()->startOfDay(), $hoy->copy()->endOfDay()],
        };
    }

    private static function fechaValida(?string $fecha): ?Carbon
    {
        if (!$fecha) {
            return null;
        }

        try {
            return Carbon::parse($fecha);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Query base de pedidos válidos (sin cancelados) para un rango de fechas.
     */
    public static function pedidosQuery(?Carbon $desde = null, ?Carbon $hasta = null): Builder
    {
        $query = Pedido::query()->whereNotIn('estado', ['cancelado']);

        if ($desde) {
            $query->where('pedidos.created_at', '>=', $desde);
        }
        if ($hasta) {
            $query->where('pedidos.created_at', '<=', $hasta);
        }

        return $query;
    }

    /**
     * Tiempo de entrega (pedido -> finalizado) de los pedidos finalizados en el rango.
     * Usa fecha_entrega (se guarda al finalizar) - created_at.
     * Devuelve pedidos medidos, promedio, mediana, mínimo y máximo en minutos.
     */
    public static function tiemposEntrega(?Carbon $desde = null, ?Carbon $hasta = null): array
    {
        $minutos = self::pedidosQuery($desde, $hasta)
            ->where('pedidos.estado', 'finalizado')
            ->whereNotNull('pedidos.fecha_entrega')
            ->whereColumn('pedidos.fecha_entrega', '>=', 'pedidos.created_at')
            ->selectRaw('TIMESTAMPDIFF(MINUTE, pedidos.created_at, pedidos.fecha_entrega) as mins')
            ->pluck('mins')
            ->map(fn ($v) => (int) $v)
            ->sort()
            ->values();

        $total = $minutos->count();

        if ($total === 0) {
            return [
                'pedidos' => 0,
                'promedio' => 0,
                'mediana' => 0,
                'minimo' => 0,
                'maximo' => 0,
            ];
        }

        $mediana = $total % 2 === 1
            ? $minutos[(int) ($total - 1) / 2]
            : (int) round(($minutos[$total / 2 - 1] + $minutos[$total / 2]) / 2);

        return [
            'pedidos' => $total,
            'promedio' => (int) round($minutos->sum() / $total),
            'mediana' => $mediana,
            'minimo' => $minutos->first(),
            'maximo' => $minutos->last(),
        ];
    }

    /**
     * Comparativas: semana actual/anterior (martes a lunes), mes actual/anterior y acumulado anual.
     */
    public static function comparativa(): array
    {
        $hoy = now();

        // Semana del negocio: martes -> lunes
        $daysSinceTuesday = ((int) $hoy->dayOfWeek + 5) % 7;
        $martesSemanaActual = $hoy->copy()->subDays($daysSinceTuesday)->startOfDay();
        $lunesSiguiente = $martesSemanaActual->copy()->addDays(6)->endOfDay();
        $martesSemanaAnterior = $martesSemanaActual->copy()->subWeek();
        $lunesAnterior = $martesSemanaAnterior->copy()->addDays(6)->endOfDay();

        $semanaActual = (float) self::pedidosQuery($martesSemanaActual, $lunesSiguiente)->sum('total');
        $pedidosSemanaActual = self::pedidosQuery($martesSemanaActual, $lunesSiguiente)->count();

        $semanaAnterior = (float) self::pedidosQuery($martesSemanaAnterior, $lunesAnterior)->sum('total');
        $pedidosSemanaAnterior = self::pedidosQuery($martesSemanaAnterior, $lunesAnterior)->count();

        $diferenciaSemanal = $semanaActual - $semanaAnterior;
        $porcentajeSemanal = $semanaAnterior > 0
            ? (($semanaActual - $semanaAnterior) / $semanaAnterior) * 100
            : 0;

        // Mes actual / anterior
        $inicioMesActual = $hoy->copy()->startOfMonth();
        $inicioMesAnterior = $inicioMesActual->copy()->subMonth();
        $finMesAnterior = $inicioMesActual->copy()->subDay()->endOfDay();

        $mesActual = (float) self::pedidosQuery($inicioMesActual, $hoy->copy()->endOfDay())->sum('total');
        $pedidosMesActual = self::pedidosQuery($inicioMesActual, $hoy->copy()->endOfDay())->count();

        $mesAnterior = (float) self::pedidosQuery($inicioMesAnterior, $finMesAnterior)->sum('total');
        $pedidosMesAnterior = self::pedidosQuery($inicioMesAnterior, $finMesAnterior)->count();

        $diferenciaMensual = $mesActual - $mesAnterior;
        $porcentajeMensual = $mesAnterior > 0
            ? (($mesActual - $mesAnterior) / $mesAnterior) * 100
            : 0;

        // Acumulado anual
        $inicioAnio = $hoy->copy()->startOfYear();
        $acumuladoAnual = (float) self::pedidosQuery($inicioAnio, $hoy->copy()->endOfDay())->sum('total');
        $pedidosAcumuladoAnual = self::pedidosQuery($inicioAnio, $hoy->copy()->endOfDay())->count();

        $mesesConDatos = (int) self::pedidosQuery($inicioAnio, $hoy->copy()->endOfDay())
            ->selectRaw('COUNT(DISTINCT DATE_FORMAT(created_at, "%Y-%m")) as meses')
            ->value('meses') ?? 1;

        $promedioMensualAnual = $mesesConDatos > 0
            ? $acumuladoAnual / $mesesConDatos
            : 0;

        return [
            'semanaActual' => $semanaActual,
            'pedidosSemanaActual' => $pedidosSemanaActual,
            'semanaAnterior' => $semanaAnterior,
            'pedidosSemanaAnterior' => $pedidosSemanaAnterior,
            'diferenciaSemanal' => $diferenciaSemanal,
            'porcentajeSemanal' => $porcentajeSemanal,
            'mesActual' => $mesActual,
            'pedidosMesActual' => $pedidosMesActual,
            'mesAnterior' => $mesAnterior,
            'pedidosMesAnterior' => $pedidosMesAnterior,
            'diferenciaMensual' => $diferenciaMensual,
            'porcentajeMensual' => $porcentajeMensual,
            'acumuladoAnual' => $acumuladoAnual,
            'pedidosAcumuladoAnual' => $pedidosAcumuladoAnual,
            'promedioMensualAnual' => $promedioMensualAnual,
            'mesesConDatos' => max(1, $mesesConDatos),
        ];
    }

    /**
     * Desglose de los últimos N meses (incluye el mes en curso).
     * Devuelve una fila por mes, con ceros cuando no hubo ventas.
     */
    public static function desgloseMensual(int $meses = 12): array
    {
        $inicio = now()->copy()->startOfMonth()->subMonths($meses - 1);
        $fin = now()->copy()->endOfMonth();

        $rows = self::pedidosQuery($inicio, $fin)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as periodo, COUNT(*) as pedidos, SUM(total) as ventas')
            ->groupBy('periodo')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[$row->periodo] = [
                'pedidos' => (int) $row->pedidos,
                'ventas' => (float) $row->ventas,
            ];
        }

        $nombres = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $resultado = [];
        $ventasAnterior = null;
        $claveActual = now()->format('Y-m');

        for ($i = 0; $i < $meses; $i++) {
            $mes = $inicio->copy()->addMonths($i);
            $clave = $mes->format('Y-m');
            $ventas = $map[$clave]['ventas'] ?? 0;
            $pedidos = $map[$clave]['pedidos'] ?? 0;

            $resultado[] = [
                'clave' => $clave,
                'label' => $nombres[(int) $mes->month] . ' ' . $mes->year,
                'mes' => (int) $mes->month,
                'anio' => (int) $mes->year,
                'pedidos' => $pedidos,
                'ventas' => $ventas,
                'promedio' => $pedidos > 0 ? $ventas / $pedidos : 0,
                'variacion' => ($ventasAnterior !== null && $ventasAnterior > 0)
                    ? (($ventas - $ventasAnterior) / $ventasAnterior) * 100
                    : null,
                'actual' => $clave === $claveActual,
            ];

            $ventasAnterior = $ventas;
        }

        return $resultado;
    }
}
