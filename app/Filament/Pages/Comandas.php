<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Pedidos\PedidoResource;
use App\Models\NegocioSetting;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\Punto;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use UnitEnum;

class Comandas extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel = 'PDV';
    protected static ?string $title = 'Punto de Venta';
    protected static ?string $slug = 'comandas';
    protected static ?int $navigationSort = 1;
    protected static string | UnitEnum | null $navigationGroup = 'Punto de Venta';

    protected string $view = 'filament.pages.comandas';

    public static function canAccess(): bool
    {
        return auth()->user() && in_array(auth()->user()->role, ['admin', 'cajero']);
    }

    public static function getBadge(): ?string
    {
        $count = Pedido::where('estado', 'pendiente_pago')->whereDate('created_at', today())->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getBadgeColor(): ?string
    {
        return 'warning';
    }

    public $pendientePago = [];
    public $enProceso = [];
    public $enCamino = [];
    public $haLlegado = [];
    public $todos = [];
    public $prevPendientePagoIds = '';
    public $pdvNuevosPedidosJson = '';
    public $vistaLista = true;
    public $pedidos_pausados = false;

    public $modalPago = false;
    public $pedidoPagoId = null;
    public $pedidoNumero = '';
    public $pedidoEstado = '';
    public $pedidoOrigen = '';
    public $pedidoFecha = '';
    public $pedidoMetodoPago = '';
    public $pedidoSubtotal = 0;
    public $pagoMetodo = 'efectivo';
    public $pagoMonto = 0;
    public $pagoReferencia = '';
    public $pagosRegistrados = [];
    public $totalPagado = 0;
    public $totalPedido = 0;
    public $productosPedido = [];
    public $clienteNombre = '';
    public $clienteTelefono = '';
    public $clienteConjunto = '';
    public $clienteTorre = '';
    public $clienteApto = '';
    public $clienteDireccion = '';
    public $descuentoTipo = 'fijo';
    public $descuentoValor = 0;
    public $descuentoAplicado = 0;
    public $totalConDescuento = 0;
    public $pagoError = '';

    public function mount(): void
    {
        $this->pedidos_pausados = NegocioSetting::isPaused();
        $this->cargarPedidos();
    }

    #[On('pedidoActualizado')]
    public function cargarPedidos(): void
    {
        $this->pedidos_pausados = NegocioSetting::isPaused();
        // Convertir pedidos antiguos "pendiente" a "en_proceso"
        Pedido::where('estado', 'pendiente')->update(['estado' => 'en_proceso']);

        $this->pendientePago = Pedido::with('cliente')
            ->where('estado', 'pendiente_pago')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $idsAhora = array_column($this->pendientePago, 'id');
        $idsPrevios = $this->prevPendientePagoIds ? explode(',', $this->prevPendientePagoIds) : [];

        if (!empty($idsPrevios)) {
            $nuevosIds = array_diff($idsAhora, $idsPrevios);
            if (!empty($nuevosIds)) {
                $nuevosIds = array_values($nuevosIds);
                $nuevosPedidos = Pedido::with('cliente')->whereIn('id', $nuevosIds)->get()->toArray();
                $this->pdvNuevosPedidosJson = json_encode(['pedidos' => $nuevosPedidos]);
            } else {
                $this->pdvNuevosPedidosJson = '';
            }
        } else {
            $this->pdvNuevosPedidosJson = '';
        }

        $this->prevPendientePagoIds = implode(',', $idsAhora);

        $this->enProceso = Pedido::with('cliente')
            ->where('estado', 'en_proceso')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();

        $this->enCamino = Pedido::with('cliente')
            ->where('estado', 'en_camino')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();

        $this->haLlegado = Pedido::with('cliente')
            ->where('estado', 'entregado')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();

        foreach ($this->haLlegado as &$p) {
            $totalPagado = (float) Pago::where('pedido_id', $p['id'])->where('confirmado', true)->sum('monto');
            $total = (float) ($p['total'] ?? 0);
            $p['pago_completo'] = $total > 0 && $totalPagado >= $total;
        }
        unset($p);

        $this->todos = Pedido::with('cliente')
            ->whereIn('estado', ['pendiente_pago', 'en_proceso', 'en_camino', 'entregado'])
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function togglePausar(): void
    {
        if (!auth()->user()->isAdmin()) return;

        $settings = NegocioSetting::getSettings();
        $settings->update(['pedidos_pausados' => !$this->pedidos_pausados]);
        $this->pedidos_pausados = !$this->pedidos_pausados;

        $estado = $this->pedidos_pausados ? 'pausados' : 'reactivados';
        Notification::make()
            ->title("Pedidos web {$estado}")
            ->success()
            ->send();
    }

    public function cambiarEstado(int $pedidoId, string $nuevoEstado): void
    {
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) return;

        $estados = ['pendiente_pago', 'en_proceso', 'en_camino', 'entregado'];
        if (!in_array($nuevoEstado, $estados)) return;

        $etiquetas = [
            'pendiente_pago' => '⏳ Pendiente de Pago',
            'en_proceso' => '👨‍🍳 En preparación',
            'en_camino' => '🚗 En camino',
            'entregado' => '📍 Ha llegado',
        ];

        $pedido->update(['estado' => $nuevoEstado]);

        Notification::make()
            ->title("Pedido #{$pedido->numero_pedido} → {$etiquetas[$nuevoEstado]}")
            ->success()
            ->send();

        $this->cargarPedidos();
    }

    public function rechazarPedido(int $pedidoId): void
    {
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) return;

        $pedido->update(['estado' => 'cancelado']);

        Notification::make()
            ->title("Pedido #{$pedido->numero_pedido} rechazado")
            ->danger()
            ->send();

        $this->cargarPedidos();
    }

    public function cancelarPedido(int $pedidoId, string $motivo = ''): void
    {
        if (!auth()->user()->canCancelarPedido()) {
            Notification::make()
                ->title('No tienes permiso para cancelar pedidos')
                ->danger()
                ->send();
            return;
        }

        $pedido = Pedido::find($pedidoId);
        if (!$pedido) return;

        $pedido->update([
            'estado' => 'cancelado',
            'motivo_cancelacion' => $motivo,
        ]);

        Notification::make()
            ->title("Pedido #{$pedido->numero_pedido} cancelado")
            ->danger()
            ->send();

        $this->cargarPedidos();
    }

    public function pagoCompleto(int $pedidoId): bool
    {
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) return false;
        $totalPagado = (float) Pago::where('pedido_id', $pedidoId)->where('confirmado', true)->sum('monto');
        $total = (float) $pedido->total;
        return $total > 0 && $totalPagado >= $total;
    }

    public function abrirModalPago(int $pedidoId): void
    {
        $pedido = Pedido::with('pagos', 'cliente')->find($pedidoId);
        if (!$pedido) return;

        $this->pedidoPagoId = $pedidoId;
        $this->pedidoNumero = $pedido->numero_pedido;
        $this->pedidoEstado = $pedido->estado;
        $this->pedidoOrigen = $pedido->origen ?? 'pdv';
        $this->pedidoFecha = $pedido->created_at->setTimezone('America/Bogota')->format('d/m/y H:i');
        $this->pedidoMetodoPago = $pedido->metodo_pago;
        $this->pedidoSubtotal = (float) $pedido->subtotal;
        $this->totalPedido = (float) $pedido->total;
        $this->pagoMetodo = $pedido->metodo_pago ?? 'efectivo';
        $this->pagoMonto = 0;
        $this->pagoReferencia = '';
        $this->descuentoTipo = 'fijo';
        $this->descuentoValor = 0;
        $this->descuentoAplicado = 0;
        $this->totalConDescuento = (float) $pedido->total;
        $this->clienteNombre = $pedido->cliente?->nombre ?? '';
        $this->clienteTelefono = $pedido->cliente?->telefono ?? '';
        $this->clienteConjunto = $pedido->cliente?->conjunto ?? '';
        $this->clienteTorre = $pedido->cliente?->torre ?? '';
        $this->clienteApto = $pedido->cliente?->apto ?? '';
        $dir = $pedido->cliente;
        $this->clienteDireccion = $dir ? collect(array_filter([$dir->direccion, $dir->conjunto, $dir->torre ? "torre {$dir->torre}" : null, $dir->apto ? "apto {$dir->apto}" : null]))->implode(', ') : '';
        $this->productosPedido = PedidoProducto::with('producto')
            ->where('pedido_id', $pedidoId)
            ->get()
            ->toArray();
        $this->cargarPagos();
        $this->pagoMonto = max(0, $this->totalConDescuento - $this->totalPagado);
        $this->modalPago = true;
    }

    public function cargarPagos(): void
    {
        $this->pagoError = '';
        $this->pagosRegistrados = Pago::where('pedido_id', $this->pedidoPagoId)
            ->where('confirmado', true)->get()->toArray();
        $this->totalPagado = (float) array_sum(array_column($this->pagosRegistrados, 'monto'));
    }

    public function updatedDescuentoTipo(): void
    {
        $this->actualizarDescuento();
    }

    public function updatedDescuentoValor(): void
    {
        $this->actualizarDescuento();
    }

    public function aplicarDescuento(): void
    {
        $this->actualizarDescuento();
    }

    private function actualizarDescuento(): void
    {
        $val = (float) ($this->descuentoValor ?: 0);
        if ($this->descuentoTipo === 'porcentaje') {
            $val = min(100, max(0, $val));
            $this->descuentoAplicado = round($this->totalPedido * $val / 100, 0);
        } else {
            $val = min($this->totalPedido, max(0, $val));
            $this->descuentoAplicado = $val;
        }
        $this->totalConDescuento = max(0, $this->totalPedido - $this->descuentoAplicado);
    }

    public function guardarCliente(): void
    {
        $pedido = Pedido::find($this->pedidoPagoId);
        if (!$pedido || !$pedido->cliente) return;

        $pedido->cliente->update([
            'nombre' => $this->clienteNombre,
            'telefono' => $this->clienteTelefono,
            'conjunto' => $this->clienteConjunto,
            'torre' => $this->clienteTorre,
            'apto' => $this->clienteApto,
        ]);

        Notification::make()
            ->title('Datos del cliente actualizados')
            ->success()
            ->send();
    }

    public function cerrarModalPago(): void
    {
        $this->modalPago = false;
        $this->pedidoPagoId = null;
        $this->pagosRegistrados = [];
        $this->cargarPedidos();
    }

    public function aceptarDesdeModal(): void
    {
        $pedidoId = $this->pedidoPagoId;
        $this->cerrarModalPago();
        $this->cambiarEstado($pedidoId, 'en_proceso');
    }

    public function finalizarDesdeModal(): void
    {
        $pedido = Pedido::find($this->pedidoPagoId);
        if (!$pedido) return;

        $totalPagado = (float) Pago::where('pedido_id', $pedido->id)->where('confirmado', true)->sum('monto');
        $total = (float) $pedido->total;

        if ($total <= 0 || $totalPagado < $total) {
            Notification::make()
                ->title('No se puede finalizar: debe registrar el pago completo primero')
                ->danger()
                ->send();
            return;
        }

        $pedido->update(['estado' => 'finalizado', 'fecha_entrega' => now()]);

        Notification::make()
            ->title("Pedido #{$pedido->numero_pedido} finalizado")
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'pago-modal');
        $this->cargarPedidos();
    }

    public function guardarCambios(): void
    {
        $pedido = Pedido::find($this->pedidoPagoId);
        if (!$pedido) return;

        if ($this->descuentoAplicado > 0) {
            $pedido->update([
                'descuento_manual' => $this->descuentoAplicado,
                'descuento_manual_tipo' => $this->descuentoTipo === 'fijo' ? 'monto' : $this->descuentoTipo,
                'descuento_manual_valor' => $this->descuentoValor,
                'total' => $this->totalConDescuento,
            ]);
        }

        if ($pedido->cliente) {
            $pedido->cliente->update([
                'nombre' => $this->clienteNombre,
                'telefono' => $this->clienteTelefono,
                'conjunto' => $this->clienteConjunto,
                'torre' => $this->clienteTorre,
                'apto' => $this->clienteApto,
            ]);
        }

        Notification::make()
            ->title("Pedido #{$pedido->numero_pedido} guardado")
            ->success()
            ->send();

        $this->cargarPagos();
    }

    public function registrarPago(): void
    {
        $this->validate([
            'pedidoPagoId' => 'required',
            'pagoMonto' => 'required|numeric|min:1',
            'pagoMetodo' => 'required|in:' . implode(',', array_keys(NegocioSetting::getActivePaymentMethods())),
        ]);

        $pedido = Pedido::find($this->pedidoPagoId);
        if (!$pedido) return;

        if ($this->descuentoAplicado > 0) {
            $pedido->update([
                'descuento_manual' => $this->descuentoAplicado,
                'descuento_manual_tipo' => $this->descuentoTipo,
                'descuento_manual_valor' => $this->descuentoValor,
            ]);
        }

        if ($pedido->cliente) {
            $pedido->cliente->update([
                'nombre' => $this->clienteNombre,
                'telefono' => $this->clienteTelefono,
                'conjunto' => $this->clienteConjunto,
                'torre' => $this->clienteTorre,
                'apto' => $this->clienteApto,
            ]);
        }

        $restante = $this->totalConDescuento - $this->totalPagado;
        if ($restante <= 0) {
            $this->pagoError = 'Este pedido ya está completamente pagado';
            return;
        }
        $monto = (float) $this->pagoMonto;
        if ($monto > $restante) {
            $this->pagoError = 'El monto ingresado ($' . number_format($monto, 0, ',', '.') . ') supera el saldo pendiente ($' . number_format($restante, 0, ',', '.') . ')';
            return;
        }
        $this->pagoError = '';

        Pago::create([
            'pedido_id' => $this->pedidoPagoId,
            'monto' => $monto,
            'metodo' => $this->pagoMetodo,
            'referencia' => $this->pagoReferencia ?: null,
            'confirmado' => true,
            'fecha_pago' => now(),
        ]);

        $metodosUsados = array_unique(array_merge(
            array_column($this->pagosRegistrados, 'metodo'),
            [$this->pagoMetodo]
        ));
        $pedido->update(['metodo_pago' => count($metodosUsados) > 1 ? 'mixto' : $this->pagoMetodo]);

        $this->cargarPagos();
        $this->pagoMonto = 0;
        $this->pagoMetodo = 'efectivo';
        $this->pagoReferencia = '';

        if ($this->totalPagado >= $this->totalConDescuento) {
            Notification::make()
                ->title("Pago completo para Pedido #{$pedido->numero_pedido}")
                ->success()
                ->send();
        } else {
            $restante = $this->totalConDescuento - $this->totalPagado;
            Notification::make()
                ->title("Pago de \$" . number_format($monto, 0, ',', '.') . " registrado. Restan: \$" . number_format($restante, 0, ',', '.'))
                ->success()
                ->send();
        }
    }

    public function eliminarPago(int $pagoId): void
    {
        $pago = Pago::find($pagoId);
        if (!$pago) return;

        $pedidoId = $pago->pedido_id;
        $pago->delete();

        $this->cargarPagos();

        $metodosRestantes = array_unique(array_column($this->pagosRegistrados, 'metodo'));
        if (!empty($metodosRestantes)) {
            Pedido::find($pedidoId)->update([
                'metodo_pago' => count($metodosRestantes) > 1 ? 'mixto' : $metodosRestantes[0],
            ]);
        }

        Notification::make()
            ->title("Pago eliminado")
            ->success()
            ->send();
    }

    public function finalizarPedido(int $pedidoId): void
    {
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) return;

        if ($pedido->estado !== 'entregado') {
            Notification::make()
                ->title('No se puede finalizar: el pedido debe estar en "Ha Llegado"')
                ->danger()
                ->send();
            return;
        }

        if (!$this->pagoCompleto($pedidoId)) {
            $this->abrirModalPago($pedidoId);
            return;
        }

        $pedido->update(['estado' => 'finalizado', 'fecha_entrega' => now()]);

        Notification::make()
            ->title("Pedido #{$pedidoId} finalizado")
            ->success()
            ->send();

        $this->cargarPedidos();
    }

    public function editarPedido(int $pedidoId): void
    {
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) return;

        $this->abrirModalPago($pedidoId);
    }

    public function eliminarPedido(int $pedidoId): void
    {
        if (!auth()->user()->isAdmin()) {
            Notification::make()
                ->title('Solo administradores pueden eliminar pedidos')
                ->danger()
                ->send();
            return;
        }

        $pedido = Pedido::find($pedidoId);
        if (!$pedido) return;

        Punto::where('pedido_id', $pedidoId)->delete();
        Pago::where('pedido_id', $pedidoId)->delete();
        PedidoProducto::where('pedido_id', $pedidoId)->delete();
        $pedido->delete();

        Notification::make()
            ->title("Pedido #{$pedidoId} eliminado")
            ->danger()
            ->send();

        $this->cargarPedidos();
    }

    public function getProductosPedido(int $pedidoId): array
    {
        return PedidoProducto::with('producto')
            ->where('pedido_id', $pedidoId)
            ->get()
            ->toArray();
    }

    public function tiempoTranscurrido(string $fecha): string
    {
        $creado = \Carbon\Carbon::parse($fecha);
        $totalSec = (int) $creado->diffInSeconds(now());

        if ($totalSec < 60) {
            $s = str_pad($totalSec, 2, '0', STR_PAD_LEFT);
            return "00:{$s}";
        }
        $minutos = intdiv($totalSec, 60);
        if ($minutos > 60) return '60:00';
        $segundos = $totalSec % 60;
        return str_pad($minutos, 2, '0', STR_PAD_LEFT) . ':' . str_pad($segundos, 2, '0', STR_PAD_LEFT);
    }
}
