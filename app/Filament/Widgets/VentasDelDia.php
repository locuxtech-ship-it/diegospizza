<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VentasDelDia extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $hoy = now()->startOfDay();

        $totalVentas = Pedido::whereDate('created_at', $hoy)
            ->whereNotIn('estado', ['cancelado'])
            ->sum('total');

        $totalPedidos = Pedido::whereDate('created_at', $hoy)
            ->whereNotIn('estado', ['cancelado'])
            ->count();

        $pendientes = Pedido::whereDate('created_at', $hoy)
            ->where('estado', 'pendiente')
            ->count();

        $pedidosHoy = Pedido::whereDate('created_at', $hoy)
            ->whereNotIn('estado', ['cancelado'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'cliente' => $p->cliente->nombre,
                'total' => $p->total,
                'estado' => $p->estado,
                'hora' => $p->created_at->format('H:i'),
            ]);

        $promedio = $totalPedidos > 0 ? $totalVentas / $totalPedidos : 0;

        return [
            Stat::make('Ventas del día', '$' . number_format($totalVentas, 0, ',', '.'))
                ->description('Total de ventas hoy')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pedidos hoy', $totalPedidos)
                ->description("Promedio: $" . number_format($promedio, 0, ',', '.') . " por pedido")
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),

            Stat::make('Pendientes', $pendientes)
                ->description('Por preparar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
