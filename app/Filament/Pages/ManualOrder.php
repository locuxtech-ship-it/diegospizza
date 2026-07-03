<?php

namespace App\Filament\Pages;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\NegocioSetting;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\Producto;
use App\Models\ProductoVariant;
use App\Models\Punto;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use UnitEnum;

class ManualOrder extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Orden Manual';
    protected static ?string $title = 'Orden Manual';
    protected static ?string $slug = 'nuevo-pedido';
    protected static ?int $navigationSort = 0;
    protected static string | UnitEnum | null $navigationGroup = 'Punto de Venta';

    protected string $view = 'filament.pages.manual-order';

    public $categoriaActiva = null;
    public $carrito = [];
    public $nombre = '';
    public $telefono = '';
    public $conjunto = '';
    public $torre = '';
    public $apto = '';
    public $notas = '';
    public $metodoPago = 'efectivo';
    public $pedidoCreado = false;
    public $pedidoId = null;
    public $total = 0;

    public $direccionesDisponibles = [];
    public $direccionSeleccionadaId = null;
    public $guardarDireccion = false;

    public $productoMitad = null;
    public $mitad1 = null;
    public $mitad2 = null;
    public $saboresPizza = [];
    public $clienteInfo = null;

    public function mount(): void
    {
        $this->carrito = session()->get('manual_carrito', []);
        $this->recalcularTotal();
    }

    public function updatedTelefono()
    {
        $cliente = Cliente::where('telefono', $this->telefono)->first();
        $this->direccionesDisponibles = [];
        $this->direccionSeleccionadaId = null;

        if ($cliente) {
            $totalPedidos = $cliente->pedidos()->count();
            $this->clienteInfo = [
                'nombre' => $cliente->nombre,
                'total_pedidos' => $totalPedidos,
                'puntos' => $cliente->puntos_acumulados,
                'clasificacion' => $cliente->clasificacion,
                'clasificacion_label' => $cliente->clasificacion_label,
            ];
            if (empty($this->nombre)) $this->nombre = $cliente->nombre;

            $this->direccionesDisponibles = $cliente->direcciones()
                ->orderBy('es_principal', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();

            if (!empty($this->direccionesDisponibles)) {
                $this->seleccionarDireccion($this->direccionesDisponibles[0]['id']);
            } elseif ($cliente->conjunto) {
                $this->conjunto = $cliente->conjunto;
                $this->torre = $cliente->torre ?? '';
                $this->apto = $cliente->apto ?? '';
            }
        } else {
            $this->clienteInfo = null;
        }
    }

    public function seleccionarDireccion(?int $direccionId): void
    {
        $this->direccionSeleccionadaId = $direccionId;
        if ($direccionId === null) {
            $this->conjunto = '';
            $this->torre = '';
            $this->apto = '';
            return;
        }
        $dir = collect($this->direccionesDisponibles)->firstWhere('id', $direccionId);
        if ($dir) {
            $this->conjunto = $dir['conjunto'];
            $this->torre = $dir['torre'] ?? '';
            $this->apto = $dir['apto'] ?? '';
        }
    }

    public function abrirMitadYMitad(int $productoId): void
    {
        $this->productoMitad = Producto::find($productoId);
        $this->mitad1 = null;
        $this->mitad2 = null;
        $this->saboresPizza = Producto::where('disponible', true)
            ->where('es_personalizable', false)
            ->where('id', '!=', $productoId)
            ->whereHas('categoria', fn ($q) => $q->where('es_pizza', true))
            ->orderBy('nombre')
            ->get();
    }

    public function cerrarMitadYMitad(): void
    {
        $this->productoMitad = null;
        $this->mitad1 = null;
        $this->mitad2 = null;
        $this->saboresPizza = [];
    }

    public function agregarMitadYMitad(): void
    {
        if (!$this->mitad1 || !$this->mitad2 || !$this->productoMitad) return;

        $sabor1 = Producto::find($this->mitad1);
        $sabor2 = Producto::find($this->mitad2);
        if (!$sabor1 || !$sabor2) return;

        $vars1 = ProductoVariant::where('producto_id', $sabor1->id)->get();
        $vars2 = ProductoVariant::where('producto_id', $sabor2->id)->get();
        $mediana1 = $vars1->first(fn($v) => stripos($v->tamanio, 'mediana') !== false || stripos($v->tamanio, 'mediano') !== false);
        $mediana2 = $vars2->first(fn($v) => stripos($v->tamanio, 'mediana') !== false || stripos($v->tamanio, 'mediano') !== false);
        $precioSabor1 = (float) ($mediana1?->precio ?? $vars1->first()?->precio ?? $sabor1->precio);
        $precioSabor2 = (float) ($mediana2?->precio ?? $vars2->first()?->precio ?? $sabor2->precio);
        $precio = max($precioSabor1, $precioSabor2);
        $mitades = [
            ['producto_id' => $sabor1->id, 'nombre' => $sabor1->nombre, 'precio' => $precioSabor1],
            ['producto_id' => $sabor2->id, 'nombre' => $sabor2->nombre, 'precio' => $precioSabor2],
        ];

        $key = $this->productoMitad->id . '_mitad_' . md5(json_encode($mitades));
        $nombre = $this->productoMitad->nombre . ' (Mitad y Mitad)';

        if (isset($this->carrito[$key])) {
            $this->carrito[$key]['cantidad']++;
        } else {
            $this->carrito[$key] = [
                'producto_id' => $this->productoMitad->id,
                'variant_id' => null,
                'variant_tamanio' => null,
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => 1,
                'mitades' => $mitades,
            ];
        }

        $this->guardarCarrito();
        $this->recalcularTotal();
        $this->cerrarMitadYMitad();
    }

    public function agregarAlCarrito(int $productoId, ?int $variantId = null): void
    {
        $producto = Producto::with('variants')->find($productoId);
        if (!$producto) return;

        if ($producto->es_personalizable) {
            $this->abrirMitadYMitad($productoId);
            return;
        }

        $precio = (float) $producto->precio;
        $nombre = $producto->nombre;
        $variantTamanio = null;

        if ($variantId) {
            $variant = $producto->variants->firstWhere('id', $variantId);
            if ($variant) {
                $precio = (float) $variant->precio;
                $variantTamanio = $variant->tamanio;
            }
        } elseif ($producto->variants->isNotEmpty()) {
            $variant = $producto->variants->first();
            $precio = (float) $variant->precio;
            $variantId = $variant->id;
            $variantTamanio = $variant->tamanio;
        }

        $key = $productoId . '_' . ($variantId ?? '0');

        if (isset($this->carrito[$key])) {
            $this->carrito[$key]['cantidad']++;
        } else {
            $this->carrito[$key] = [
                'producto_id' => $productoId,
                'variant_id' => $variantId,
                'variant_tamanio' => $variantTamanio,
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => 1,
            ];
        }

        $this->guardarCarrito();
        $this->recalcularTotal();
    }

    public function cambiarCantidad(string $key, int $delta): void
    {
        if (!isset($this->carrito[$key])) return;

        $nueva = $this->carrito[$key]['cantidad'] + $delta;

        if ($nueva <= 0) {
            unset($this->carrito[$key]);
        } else {
            $this->carrito[$key]['cantidad'] = $nueva;
        }

        $this->guardarCarrito();
        $this->recalcularTotal();
    }

    public function quitarDelCarrito(string $key): void
    {
        unset($this->carrito[$key]);
        $this->guardarCarrito();
        $this->recalcularTotal();
    }

    public function limpiarCarrito(): void
    {
        $this->carrito = [];
        $this->guardarCarrito();
        $this->recalcularTotal();
    }

    public function procesarPedido(): void
    {
        $activos = array_keys(NegocioSetting::getActivePaymentMethods());
        $this->validate([
            'nombre' => 'required|min:3',
            'telefono' => 'required|min:7',
            'conjunto' => 'required|min:3',
            'carrito' => 'required|array|min:1',
            'metodoPago' => 'required|in:' . implode(',', $activos),
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'conjunto.required' => 'El conjunto es obligatorio',
            'conjunto.min' => 'El conjunto debe tener al menos 3 caracteres',
            'carrito.min' => 'Agrega al menos un producto',
        ]);

        $cliente = Cliente::firstOrCreate(
            ['telefono' => $this->telefono],
            ['nombre' => $this->nombre, 'direccion' => '', 'conjunto' => $this->conjunto, 'torre' => $this->torre, 'apto' => $this->apto]
        );

        $cliente->update([
            'nombre' => $this->nombre,
            'direccion' => '',
            'notas' => $this->notas ?: $cliente->notas,
            'conjunto' => $this->conjunto,
            'torre' => $this->torre,
            'apto' => $this->apto,
        ]);

        $direccionId = null;
        if ($this->direccionSeleccionadaId && collect($this->direccionesDisponibles)->pluck('id')->contains($this->direccionSeleccionadaId)) {
            $direccionId = $this->direccionSeleccionadaId;
        } elseif ($this->guardarDireccion) {
            $dir = \App\Models\ClienteDireccion::create([
                'cliente_id' => $cliente->id,
                'alias' => 'Dirección ' . ($cliente->direcciones()->count() + 1),
                'conjunto' => $this->conjunto,
                'torre' => $this->torre,
                'apto' => $this->apto,
                'es_principal' => $cliente->direcciones()->count() === 0,
            ]);
            $direccionId = $dir->id;
        }

        $subtotal = 0;
        foreach ($this->carrito as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }

        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'cliente_direccion_id' => $direccionId,
            'subtotal' => $subtotal,
            'descuento_puntos' => 0,
            'total' => $subtotal,
            'origen' => 'pdv',
            'estado' => 'pendiente_pago',
            'metodo_pago' => $this->metodoPago,
            'notas' => $this->notas,
        ]);

        foreach ($this->carrito as $item) {
            PedidoProducto::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $item['producto_id'],
                'variant_id' => $item['variant_id'] ?: null,
                'variant_tamanio' => $item['variant_tamanio'] ?? null,
                'mitades' => $item['mitades'] ?? null,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'subtotal' => $item['precio'] * $item['cantidad'],
            ]);
        }

        Pago::create([
            'pedido_id' => $pedido->id,
            'monto' => $subtotal,
            'metodo' => $this->metodoPago,
            'confirmado' => false,
            'fecha_pago' => now(),
        ]);

        $settings = NegocioSetting::getSettings();
        $montoPor = (float) ($settings->puntos_ganancia_monto ?? 100);
        $valorPor = (int) ($settings->puntos_ganancia_valor ?? 1);
        $puntosGanados = $montoPor > 0 ? (int) (($subtotal / $montoPor) * $valorPor) : 0;
        if ($puntosGanados > 0) {
            Punto::create([
                'cliente_id' => $cliente->id,
                'puntos' => $puntosGanados,
                'concepto' => "Compra PDV #{$pedido->numero_pedido}",
                'pedido_id' => $pedido->id,
            ]);
            $cliente->increment('puntos_acumulados', $puntosGanados);
        }

        $this->limpiarCarrito();
        $this->pedidoCreado = true;
        $this->pedidoId = $pedido->id;

        Notification::make()
            ->title("Pedido #{$pedido->numero_pedido} creado correctamente")
            ->success()
            ->send();

        $this->dispatch('pedidoActualizado');
    }

    public function reiniciar(): void
    {
        $this->pedidoCreado = false;
        $this->pedidoId = null;
        $this->nombre = '';
        $this->telefono = '';
        $this->direccion = '';
        $this->conjunto = '';
        $this->torre = '';
        $this->apto = '';
        $this->notas = '';
        $this->metodoPago = 'efectivo';
    }

    private function guardarCarrito(): void
    {
        session()->put('manual_carrito', $this->carrito);
    }

    private function recalcularTotal(): void
    {
        $this->total = array_sum(array_map(fn ($i) => $i['precio'] * $i['cantidad'], $this->carrito));
    }

    protected function getViewData(): array
    {
        $categorias = Categoria::where('activo', true)
            ->with(['productosDisponibles' => function ($q) {
                $q->with('variants');
            }])
            ->orderBy('orden')
            ->get();

        $categoriasFiltradas = $this->categoriaActiva
            ? $categorias->where('id', $this->categoriaActiva)
            : $categorias;

        return compact('categorias', 'categoriasFiltradas');
    }
}
