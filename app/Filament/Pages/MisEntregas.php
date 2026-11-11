<?php

namespace App\Filament\Pages;

use App\Models\Pedido;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class MisEntregas extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTruck;
    protected static ?string $navigationLabel = 'Mis Entregas';
    protected static ?string $title = '🛵 Mis Entregas';
    protected static ?string $slug = 'mis-entregas';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.mis-entregas';

    /** Pedidos asignados aún en preparación (pendiente_pago / en_proceso) */
    public array $enPreparacion = [];

    /** Pedidos asignados que ya salieron a ruta (en_camino) */
    public array $enReparto = [];

    /** Pedidos asignados entregados hoy (marcados por este repartidor) */
    public array $entregadosHoy = [];

    /** Entregas de días anteriores (histórico personal, paginado) */
    public array $entregadosAnteriores = [];

    /** Cuántos pedidos del histórico carga a la vez */
    public int $historialLimite = 20;

    public static function canAccess(): bool
    {
        return auth()->user()?->isRepartidor() ?? false;
    }

    public function mount(): void
    {
        $this->cargarPedidos();
    }

    public function cargarPedidos(): void
    {
        $repartidorId = auth()->id();

        $base = fn () => Pedido::with('cliente')
            ->where('repartidor_id', $repartidorId)
            ->orderBy('updated_at', 'desc');

        $this->enPreparacion = $base()
            ->whereIn('estado', ['pendiente_pago', 'en_proceso'])
            ->get()
            ->toArray();

        $this->enReparto = $base()
            ->where('estado', 'en_camino')
            ->get()
            ->toArray();

        $this->entregadosHoy = $base()
            ->where('estado', 'entregado')
            ->whereDate('entregado_at', today())
            ->get()
            ->toArray();

        $this->entregadosAnteriores = $base()
            ->where('estado', 'entregado')
            ->whereNotNull('entregado_at')
            ->whereDate('entregado_at', '<', today())
            ->orderBy('entregado_at', 'desc')
            ->limit($this->historialLimite)
            ->get()
            ->toArray();
    }

    /** Carga 20 entregas anteriores más */
    public function cargarMasHistorial(): void
    {
        $this->historialLimite += 20;
        $this->cargarPedidos();
    }

    /** Solo pedidos asignados a ESTE repartidor */
    private function pedidoPropio(int $pedidoId): ?Pedido
    {
        return Pedido::where('id', $pedidoId)
            ->where('repartidor_id', auth()->id())
            ->first();
    }

    /** Marca "📍 Ya llegué": guarda la hora sin cambiar el estado */
    public function llegue(int $pedidoId): void
    {
        $pedido = $this->pedidoPropio($pedidoId);
        if (!$pedido) return;

        // Nunca se pisa una marca ya registrada (doble toque)
        if ($pedido->llegada_at) {
            Notification::make()->title('Ya habías marcado tu llegada')->info()->send();
            return;
        }

        $pedido->update(['llegada_at' => now()]);

        Notification::make()
            ->title("📍 Llegaste al pedido #{$pedido->numero_pedido}")
            ->success()
            ->send();

        $this->cargarPedidos();
    }

    /** Marca "✅ Entregado": cambia el estado (entregado_at lo pone el hook del modelo) */
    public function entregue(int $pedidoId): void
    {
        $pedido = $this->pedidoPropio($pedidoId);
        if (!$pedido) return;

        if ($pedido->estado === 'entregado') {
            Notification::make()->title('Este pedido ya estaba entregado')->info()->send();
            return;
        }

        if (!in_array($pedido->estado, ['pendiente_pago', 'en_proceso', 'en_camino'])) {
            Notification::make()->title('Este pedido ya no se puede entregar')->danger()->send();
            return;
        }

        $pedido->update(['estado' => 'entregado']);

        Notification::make()
            ->title("✅ Entregado el pedido #{$pedido->numero_pedido}")
            ->success()
            ->send();

        $this->cargarPedidos();
    }
}
