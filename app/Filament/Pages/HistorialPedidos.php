<?php

namespace App\Filament\Pages;

use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class HistorialPedidos extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isCajero() ?? false;
    }
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?string $navigationLabel = 'Historial de Pedidos';
    protected static ?string $title = 'Historial de Pedidos';
    protected static ?string $slug = 'ventas/historial';
    protected static ?int $navigationSort = 1;
    protected static string | UnitEnum | null $navigationGroup = 'Ventas';

    protected string $view = 'filament.pages.historial-pedidos';

    public string $periodo = 'hoy';
    public string $fechaInicio = '';
    public string $fechaFin = '';
    public array $pedidos = [];
    public float $totalVentas = 0;
    public int $totalPedidos = 0;
    public float $totalEfectivo = 0;
    public float $totalTarjeta = 0;
    public float $totalTransferencia = 0;
    public bool $isAdmin = false;

    // Modal detalle
    public bool $modalDetalle = false;
    public array $detallePedido = [];
    public array $detalleProductos = [];
    public array $detallePagos = [];
    public float $detalleTotalPagado = 0;
    public float $detalleTotal = 0;

    public function abrirDetalle(int $pedidoId): void
    {
        $pedido = Pedido::with('cliente')->find($pedidoId);
        if (!$pedido) return;

        $this->detallePedido = $pedido->toArray();
        $this->detalleProductos = PedidoProducto::with('producto')
            ->where('pedido_id', $pedidoId)->get()->toArray();
        $this->detallePagos = Pago::where('pedido_id', $pedidoId)
            ->where('confirmado', true)->get()->toArray();
        $this->detalleTotalPagado = (float) array_sum(array_column($this->detallePagos, 'monto'));
        $this->detalleTotal = (float) $pedido->total;
        $this->modalDetalle = true;
    }

    public function cerrarDetalle(): void
    {
        $this->modalDetalle = false;
        $this->detallePedido = [];
        $this->detalleProductos = [];
        $this->detallePagos = [];
        $this->detalleTotalPagado = 0;
        $this->detalleTotal = 0;
    }

    public function mount(): void
    {
        $this->isAdmin = auth()->user()?->isAdmin() ?? false;
        $this->aplicarPeriodo('hoy');
    }

    public function aplicarPeriodo(string $periodo): void
    {
        if (!$this->isAdmin) {
            $periodo = 'hoy';
        }

        $this->periodo = $periodo;

        match ($periodo) {
            'hoy' => $this->fechaInicio = $this->fechaFin = now()->format('Y-m-d'),
            'ayer' => $this->fechaInicio = $this->fechaFin = now()->subDay()->format('Y-m-d'),
            '7d' => $this->fechaInicio = now()->subDays(7)->format('Y-m-d'),
            '30d' => $this->fechaInicio = now()->subDays(30)->format('Y-m-d'),
            default => null,
        };

        if (in_array($periodo, ['7d', '30d'])) {
            $this->fechaFin = now()->format('Y-m-d');
        }

        $this->filtrar();
    }

    public function filtrar(): void
    {
        $query = Pedido::with('cliente');

        if ($this->fechaInicio) {
            $query->whereDate('created_at', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('created_at', '<=', $this->fechaFin);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->get();

        $this->pedidos = $pedidos->toArray();

        $sinCancelados = $pedidos->reject(fn($p) => $p->estado === 'cancelado');
        $this->totalVentas = (float) $sinCancelados->sum('total');
        $this->totalPedidos = $sinCancelados->count();

        if ($this->isAdmin) {
            $this->totalEfectivo = (float) (clone $sinCancelados)->where('metodo_pago', 'efectivo')->sum('total');
            $this->totalTarjeta = (float) (clone $sinCancelados)->where('metodo_pago', 'tarjeta')->sum('total');
            $this->totalTransferencia = (float) (clone $sinCancelados)->where('metodo_pago', 'transferencia')->sum('total');
        }
    }

    public function editarPedido(int $pedidoId): void
    {
        $this->abrirDetalle($pedidoId);
    }

    public function getProductosPedido(int $pedidoId): array
    {
        return PedidoProducto::with('producto')
            ->where('pedido_id', $pedidoId)
            ->get()
            ->toArray();
    }

    public function etiquetaEstado(string $estado): string
    {
        return match ($estado) {
            'finalizado' => 'Finalizado',
            'entregado' => 'Entregado',
            'en_camino' => 'En camino',
            'en_proceso' => 'Preparando',
            'entregado' => 'Ha Llegado',
            'pendiente_pago' => 'Pendiente de Pago',
            'cancelado' => 'Cancelado',
            default => 'Pendiente',
        };
    }

    public function colorEstado(string $estado): string
    {
        return match ($estado) {
            'finalizado' => 'success',
            'entregado' => 'success',
            'en_camino' => 'info',
            'entregado' => 'success',
            'en_proceso' => 'warning',
            'pendiente_pago' => 'danger',
            'cancelado' => 'danger',
            default => 'gray',
        };
    }
}
