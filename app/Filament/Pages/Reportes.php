<?php

namespace App\Filament\Pages;

use App\Models\Pedido;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class Reportes extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title = 'Reportes';
    protected static ?string $slug = 'ventas/reportes';
    protected static ?int $navigationSort = 2;
    protected static string | UnitEnum | null $navigationGroup = 'Ventas';

    protected string $view = 'filament.pages.reportes';

    public string $periodo = 'hoy';
    public string $fechaInicio = '';
    public string $fechaFin = '';

    public float $totalVentas = 0;
    public int $totalPedidos = 0;
    public float $promedioPedido = 0;
    public array $diaMasVendido = [];
    public array $horasMasVendidas = [];
    public array $saboresMasVendidos = [];
    public array $tamaniosMasVendidos = [];
    public array $mitadesMasVendidas = [];

    public function mount(): void
    {
        $this->aplicarPeriodo('hoy');
    }

    public function aplicarPeriodo(string $periodo): void
    {
        $this->periodo = $periodo;

        match ($periodo) {
            'hoy' => $this->fechaInicio = $this->fechaFin = now()->format('Y-m-d'),
            'ayer' => $this->fechaInicio = $this->fechaFin = now()->subDay()->format('Y-m-d'),
            '7d' => $this->fechaInicio = now()->subDays(7)->format('Y-m-d'),
            '30d' => $this->fechaInicio = now()->subDays(30)->format('Y-m-d'),
            default => null,
        };

        if ($periodo !== 'personalizado') {
            $this->fechaFin = now()->format('Y-m-d');
        }

        $this->filtrar();
    }

    public function filtrar(): void
    {
        $query = Pedido::whereNotIn('estado', ['cancelado']);

        if ($this->fechaInicio) {
            $query->whereDate('pedidos.created_at', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('pedidos.created_at', '<=', $this->fechaFin);
        }

        $pedidos = $query->get();

        $this->totalVentas = (float) $pedidos->sum('total');
        $this->totalPedidos = $pedidos->count();
        $this->promedioPedido = $this->totalPedidos > 0 ? $this->totalVentas / $this->totalPedidos : 0;

        $this->cargarDiaMasVendido($query);
        $this->cargarHorasMasVendidas($query);
        $this->cargarSaboresMasVendidos($query);
        $this->cargarTamaniosMasVendidos($query);
        $this->cargarMitadesMasVendidas($query);
    }

    private function cargarDiaMasVendido($baseQuery): void
    {
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        $rows = (clone $baseQuery)
            ->select(
                DB::raw('(DAYOFWEEK(pedidos.created_at) - 1) as dia_num'),
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->groupBy(DB::raw('DAYOFWEEK(pedidos.created_at)'))
            ->orderBy('total_ventas', 'desc')
            ->get()
            ->toArray();

        $this->diaMasVendido = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $this->diaMasVendido[] = [
                'dia' => $dias[(int) $row['dia_num']] ?? 'Desconocido',
                'pedidos' => $row['total_pedidos'],
                'ventas' => (float) $row['total_ventas'],
            ];
        }
    }

    private function cargarHorasMasVendidas($baseQuery): void
    {
        $rows = (clone $baseQuery)
            ->select(
                DB::raw('HOUR(pedidos.created_at) as hora'),
                DB::raw('COUNT(*) as total_pedidos'),
                DB::raw('SUM(total) as total_ventas')
            )
            ->groupBy(DB::raw('HOUR(pedidos.created_at)'))
            ->orderBy('total_pedidos', 'desc')
            ->orderBy('total_ventas', 'desc')
            ->get()
            ->toArray();

        $this->horasMasVendidas = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $hora = (int) $row['hora'];
            $this->horasMasVendidas[] = [
                'hora' => sprintf('%02d:00 - %02d:00', $hora, $hora + 1),
                'pedidos' => $row['total_pedidos'],
                'ventas' => (float) $row['total_ventas'],
            ];
        }

        $this->horasMasVendidas = array_slice($this->horasMasVendidas, 0, 10);
    }

    public function etiquetaPeriodo(): string
    {
        return match ($this->periodo) {
            'hoy' => 'Hoy',
            'ayer' => 'Ayer',
            '7d' => 'Últimos 7 días',
            '30d' => 'Últimos 30 días',
            'personalizado' => now()->parse($this->fechaInicio)->format('d/m/Y') . ' - ' . now()->parse($this->fechaFin)->format('d/m/Y'),
            default => '',
        };
    }

    private function cargarSaboresMasVendidos($baseQuery): void
    {
        $rows = (clone $baseQuery)
            ->join('pedido_productos', 'pedidos.id', '=', 'pedido_productos.pedido_id')
            ->join('productos', 'pedido_productos.producto_id', '=', 'productos.id')
            ->select(
                'productos.nombre',
                DB::raw('SUM(pedido_productos.cantidad) as total_cantidad'),
                DB::raw('SUM(pedido_productos.subtotal) as total_ventas')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_cantidad', 'desc')
            ->limit(15)
            ->get()
            ->toArray();

        $this->saboresMasVendidos = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $this->saboresMasVendidos[] = [
                'nombre' => $row['nombre'],
                'cantidad' => (int) $row['total_cantidad'],
                'ventas' => (float) $row['total_ventas'],
            ];
        }
    }

    private function cargarTamaniosMasVendidos($baseQuery): void
    {
        $rows = (clone $baseQuery)
            ->join('pedido_productos', 'pedidos.id', '=', 'pedido_productos.pedido_id')
            ->join('producto_variants', 'pedido_productos.variant_id', '=', 'producto_variants.id')
            ->select(
                'producto_variants.tamanio',
                DB::raw('SUM(pedido_productos.cantidad) as total_cantidad'),
                DB::raw('SUM(pedido_productos.subtotal) as total_ventas')
            )
            ->whereNotNull('pedido_productos.variant_id')
            ->groupBy('producto_variants.tamanio')
            ->orderBy('total_cantidad', 'desc')
            ->get()
            ->toArray();

        $this->tamaniosMasVendidos = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $this->tamaniosMasVendidos[] = [
                'tamanio' => $row['tamanio'],
                'cantidad' => (int) $row['total_cantidad'],
                'ventas' => (float) $row['total_ventas'],
            ];
        }
    }

    private function cargarMitadesMasVendidas($baseQuery): void
    {
        $mitades = [];
        $rows = (clone $baseQuery)
            ->join('pedido_productos', 'pedidos.id', '=', 'pedido_productos.pedido_id')
            ->select(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(pedido_productos.mitades, '$[0].nombre')) as sabor"),
                DB::raw('SUM(pedido_productos.cantidad) as total_cantidad')
            )
            ->whereNotNull('pedido_productos.mitades')
            ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(pedido_productos.mitades, '$[0].nombre'))"), '!=', '')
            ->groupBy(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(pedido_productos.mitades, '$[0].nombre'))"))
            ->get()
            ->toArray();

        foreach ($rows as $row) {
            $row = (array) $row;
            if (!empty($row['sabor'])) {
                $mitades[$row['sabor']] = ($mitades[$row['sabor']] ?? 0) + (int) $row['total_cantidad'];
            }
        }

        $rows = (clone $baseQuery)
            ->join('pedido_productos', 'pedidos.id', '=', 'pedido_productos.pedido_id')
            ->select(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(pedido_productos.mitades, '$[1].nombre')) as sabor"),
                DB::raw('SUM(pedido_productos.cantidad) as total_cantidad')
            )
            ->whereNotNull('pedido_productos.mitades')
            ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(pedido_productos.mitades, '$[1].nombre'))"), '!=', '')
            ->groupBy(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(pedido_productos.mitades, '$[1].nombre'))"))
            ->get()
            ->toArray();

        foreach ($rows as $row) {
            $row = (array) $row;
            if (!empty($row['sabor'])) {
                $mitades[$row['sabor']] = ($mitades[$row['sabor']] ?? 0) + (int) $row['total_cantidad'];
            }
        }

        arsort($mitades);
        $this->mitadesMasVendidas = array_slice($mitades, 0, 10);
    }

    public function getProductosPedido(int $pedidoId): array
    {
        return \App\Models\PedidoProducto::with('producto')
            ->where('pedido_id', $pedidoId)
            ->get()
            ->toArray();
    }
}
