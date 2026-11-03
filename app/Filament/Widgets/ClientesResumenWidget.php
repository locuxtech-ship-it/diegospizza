<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Pedido;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class ClientesResumenWidget extends Widget
{
    // No se descubre automáticamente en el Dashboard: solo se usa en Clientes.
    protected static bool $isDiscovered = false;

    protected string $view = 'filament.widgets.clientes-resumen';

    protected int | string | array $columnSpan = 'full';

    /** Mes seleccionado en formato Y-m. */
    public string $mes = '';

    public function mount(): void
    {
        $this->mes = now()->format('Y-m');
    }

    public function mesAnterior(): void
    {
        $this->mes = $this->mesCarbon()->subMonth()->format('Y-m');
    }

    public function mesSiguiente(): void
    {
        $siguiente = $this->mesCarbon()->addMonth();

        if ($siguiente->lte(now()->startOfMonth())) {
            $this->mes = $siguiente->format('Y-m');
        }
    }

    public function irMesActual(): void
    {
        $this->mes = now()->format('Y-m');
    }

    private function mesCarbon(): Carbon
    {
        if (preg_match('/^\d{4}-\d{2}$/', $this->mes)) {
            try {
                return Carbon::createFromFormat('Y-m-d', $this->mes . '-01')->startOfMonth();
            } catch (\Throwable) {
                // Continúa con el mes actual
            }
        }

        return now()->startOfMonth();
    }

    protected function getViewData(): array
    {
        $inicio = $this->mesCarbon()->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        $inicioMesAnterior = $inicio->copy()->subMonth()->startOfMonth();
        $finMesAnterior = $inicio->copy()->subMonth()->endOfMonth();

        $esMesActual = $inicio->isSameMonth(now());

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
            'ranking' => $ranking,
            'totalMes' => $totalMes,
            'topVentas' => $topVentas,
            'topPorcentaje' => $topPorcentaje,
            'mesLabel' => $nombres[(int) $inicio->month] . ' ' . $inicio->year,
            'esMesActual' => $esMesActual,
            'mesInput' => $inicio->format('Y-m'),
        ];
    }
}
