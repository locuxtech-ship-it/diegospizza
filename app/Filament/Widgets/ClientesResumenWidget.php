<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Pedido;
use Filament\Widgets\Widget;

class ClientesResumenWidget extends Widget
{
    // No se descubre automáticamente en el Dashboard: solo se usa en Clientes.
    protected static bool $isDiscovered = false;

    protected string $view = 'filament.widgets.clientes-resumen';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $inicio = now()->startOfMonth();
        $fin = now()->endOfMonth();

        $inicioMesAnterior = $inicio->copy()->subMonth();
        $finMesAnterior = $inicio->copy()->subDay()->endOfDay();

        $totalClientes = Cliente::count();
        $nuevosMes = Cliente::whereBetween('created_at', [$inicio, $fin])->count();
        $nuevosMesAnterior = Cliente::whereBetween('created_at', [$inicioMesAnterior, $finMesAnterior])->count();

        $ranking = Pedido::query()
            ->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
            ->whereNotIn('pedidos.estado', ['cancelado'])
            ->whereBetween('pedidos.created_at', [$inicio, $fin])
            ->selectRaw('clientes.id as cliente_id, clientes.nombre as nombre, clientes.telefono as telefono, COUNT(*) as pedidos, SUM(pedidos.total) as ventas')
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.telefono')
            ->orderByDesc('pedidos')
            ->orderByDesc('ventas')
            ->limit(10)
            ->get();

        $totalMes = (float) Pedido::query()
            ->whereNotIn('estado', ['cancelado'])
            ->whereBetween('created_at', [$inicio, $fin])
            ->sum('total');

        $topVentas = (float) $ranking->sum('ventas');
        $topPorcentaje = $totalMes > 0 ? ($topVentas / $totalMes) * 100 : 0;

        $nombres = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return [
            'totalClientes' => $totalClientes,
            'nuevosMes' => $nuevosMes,
            'nuevosMesAnterior' => $nuevosMesAnterior,
            'diferenciaNuevos' => $nuevosMes - $nuevosMesAnterior,
            'ranking' => $ranking,
            'totalMes' => $totalMes,
            'topVentas' => $topVentas,
            'topPorcentaje' => $topPorcentaje,
            'mesLabel' => $nombres[(int) now()->month] . ' ' . now()->year,
        ];
    }
}
