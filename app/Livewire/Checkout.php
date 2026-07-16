<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\ClienteDireccion;
use App\Models\CuponDescuento;
use App\Models\NegocioSetting;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\Punto;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Checkout extends Component
{
    public $items = [];
    public $subtotal = 0;
    public $total = 0;
    public $descuentoPuntos = 0;
    public $recompensaAplicada = null;
    public $recompensasDisponibles = [];
    public $recompensaSeleccionadaIndex = null;

    public $codigoCupon = '';
    public $cuponAplicado = null;
    public $descuentoCupon = 0;
    public $errorCupon = '';

    public $nombre = '';
    public $telefono = '';
    public $conjunto = '';
    public $torre = '';
    public $apto = '';
    public $notas = '';
    public $metodoPago = 'efectivo';

    public $direccionesDisponibles = [];
    public $direccionSeleccionadaId = null;
    public $guardarDireccion = false;

    public $pedidoCreado = false;
    public $pedidoId = null;
    public $whatsappUrl = '';
    public $whatsappComprobanteUrl = '';

    public $clienteInfo = null;

    // Computed by JS on the frontend, but we expose it for the view
    public $hayDescuentosProducto = false;

    protected function rules(): array
    {
        $activos = array_keys(NegocioSetting::getActivePaymentMethods());
        return [
            'nombre' => 'required|min:3',
            'telefono' => 'required|min:10',
            'conjunto' => 'required|min:3',
            'metodoPago' => 'required|in:' . implode(',', $activos),
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio',
        'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
        'telefono.required' => 'El teléfono es obligatorio',
        'telefono.min' => 'El teléfono debe tener al menos 10 dígitos',
        'conjunto.required' => 'El conjunto es obligatorio',
        'conjunto.min' => 'El conjunto debe tener al menos 3 caracteres',
        'metodoPago.required' => 'Selecciona un método de pago',
    ];

    public function mount()
    {
        if (NegocioSetting::isPaused()) {
            session()->flash('error', 'Estamos en pausa por alta demanda. Por favor intenta más tarde.');
            return redirect()->route('menu');
        }

        $this->items = session()->get('carrito', []);
        if (empty($this->items)) {
            return redirect()->route('menu');
        }
        $this->calcularTotales();
    }

    public function calcularTotales()
    {
        $this->subtotal = array_sum(array_map(fn($item) => $item['precio'] * $item['cantidad'], $this->items));
        $this->total = $this->subtotal - $this->descuentoPuntos - $this->descuentoCupon;
        if ($this->total < 0) $this->total = 0;
        $this->hayDescuentosProducto = $this->checkHayDescuentosProducto();
    }

    private function checkHayDescuentosProducto(): bool
    {
        $ids = collect($this->items)->pluck('id')->unique()->toArray();
        if (empty($ids)) return false;

        $now = now();
        $directos = \App\Models\DescuentoProducto::where('activo', true)
            ->where('producto_id', $ids)
            ->where('fecha_inicio', '<=', $now)
            ->where('fecha_expiracion', '>=', $now)
            ->count();

        if ($directos > 0) return true;

        $cats = \App\Models\Producto::whereIn('id', $ids)->pluck('categoria_id')->unique()->filter()->toArray();
        if (empty($cats)) return false;

        return \App\Models\DescuentoProducto::where('activo', true)
            ->whereNull('producto_id')
            ->whereIn('categoria_id', $cats)
            ->where('fecha_inicio', '<=', $now)
            ->where('fecha_expiracion', '>=', $now)
            ->exists();
    }

    public function aplicarCupon()
    {
        $this->errorCupon = '';
        $codigo = trim($this->codigoCupon);
        if (empty($codigo)) {
            $this->errorCupon = 'Ingresa un código';
            return;
        }

        $cupon = CuponDescuento::where('codigo', $codigo)->first();
        if (!$cupon) {
            $this->errorCupon = 'Código inválido';
            return;
        }

        $cliente = null;
        $clienteId = null;
        if ($this->telefono) {
            $cliente = Cliente::where('telefono', $this->telefono)->first();
            $clienteId = $cliente?->id;
        }

        if (!$cupon->esValido($this->subtotal, $clienteId)) {
            $msg = 'El cupón no está disponible';
            if ($cupon->cliente_id && $clienteId && $cupon->cliente_id !== $clienteId) {
                $msg = 'Este cupón es para otro cliente';
            } elseif ($cupon->usos_maximos && $cupon->usos_actuales >= $cupon->usos_maximos) {
                $msg = 'El cupón ya no tiene usos disponibles';
            } elseif ($cupon->fecha_inicio && now()->lt($cupon->fecha_inicio)) {
                $msg = 'El cupón aún no está vigente';
            } elseif ($cupon->fecha_expiracion && now()->gt($cupon->fecha_expiracion)) {
                $msg = 'El cupón ha expirado';
            } elseif ($cupon->monto_minimo && $this->subtotal < $cupon->monto_minimo) {
                $msg = 'El pedido mínimo es de $' . number_format((float) $cupon->monto_minimo, 0, ',', '.');
            } elseif ($cupon->por_cliente && $clienteId) {
                $msg = 'Ya usaste este cupón anteriormente';
            }
            $this->errorCupon = $msg;
            return;
        }

        $this->cuponAplicado = $cupon;
        $this->descuentoCupon = $cupon->calcularDescuento($this->subtotal);
        $this->codigoCupon = $cupon->codigo;

        // Remove any points discount when coupon is applied
        if ($this->descuentoPuntos > 0) {
            $this->descuentoPuntos = 0;
            $this->recompensaAplicada = null;
            $this->recompensaSeleccionadaIndex = null;
        }

        $this->calcularTotales();
    }

    public function quitarCupon()
    {
        $this->codigoCupon = '';
        $this->cuponAplicado = null;
        $this->descuentoCupon = 0;
        $this->errorCupon = '';
        $this->calcularTotales();
    }

    public function updatedTelefono()
    {
        $this->descuentoPuntos = 0;
        $this->recompensaAplicada = null;
        $this->recompensasDisponibles = [];
        $this->recompensaSeleccionadaIndex = null;
        $this->total = $this->subtotal;
        $this->direccionesDisponibles = [];
        $this->direccionSeleccionadaId = null;

        $cliente = Cliente::where('telefono', $this->telefono)->first();
        if ($cliente) {
            $totalPedidos = $cliente->pedidos()->count();
            $settings = NegocioSetting::getSettings();
            $recompensas = collect($settings->puntos_recompensas ?? [])
                ->filter(fn($r) => $cliente->puntos_acumulados >= $r['puntos'])
                ->sortByDesc('puntos')
                ->values()
                ->toArray();
            $this->recompensasDisponibles = $recompensas;
            $this->clienteInfo = [
                'nombre' => $cliente->nombre,
                'total_pedidos' => $totalPedidos,
                'puntos' => $cliente->puntos_acumulados,
                'clasificacion' => $cliente->clasificacion,
                'clasificacion_label' => $cliente->clasificacion_label,
                'recompensas' => $recompensas,
            ];
            if (empty($this->nombre)) {
                $this->nombre = $cliente->nombre;
            }

            // Load saved addresses
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

    public function seleccionarRecompensa(?int $index)
    {
        if ($this->cuponAplicado || $this->hayDescuentosProducto) {
            $this->recompensaSeleccionadaIndex = null;
            $this->descuentoPuntos = 0;
            $this->recompensaAplicada = null;
            $this->calcularTotales();
            return;
        }

        $this->recompensaSeleccionadaIndex = $index;

        if ($index === null || !isset($this->recompensasDisponibles[$index])) {
            $this->descuentoPuntos = 0;
            $this->recompensaAplicada = null;
            $this->calcularTotales();
            return;
        }

        $recompensa = $this->recompensasDisponibles[$index];
        $this->recompensaAplicada = $recompensa;

        if ($recompensa['tipo'] === 'porcentaje') {
            $this->descuentoPuntos = min($this->subtotal * $recompensa['valor'] / 100, $this->subtotal);
        } else {
            $this->descuentoPuntos = min($recompensa['valor'], $this->subtotal);
        }
        $this->calcularTotales();
    }

    public function procesarPedido()
    {
        if (NegocioSetting::isPaused()) {
            session()->flash('error', 'Estamos en pausa por alta demanda. Por favor intenta más tarde.');
            return redirect()->route('menu');
        }

        $this->validate();

        if (empty($this->items)) {
            session()->flash('error', 'El carrito está vacío');
            return;
        }

        $cliente = Cliente::firstOrCreate(
            ['telefono' => $this->telefono],
            [
                'nombre' => $this->nombre,
                'direccion' => '',
                'conjunto' => $this->conjunto,
                'torre' => $this->torre,
                'apto' => $this->apto,
            ]
        );

        $cliente->update([
            'nombre' => $this->nombre,
            'direccion' => '',
            'notas' => $this->notas ?: $cliente->notas,
            'conjunto' => $this->conjunto,
            'torre' => $this->torre,
            'apto' => $this->apto,
        ]);

        // Save or select address
        $direccionId = null;
        if ($this->direccionSeleccionadaId && collect($this->direccionesDisponibles)->pluck('id')->contains($this->direccionSeleccionadaId)) {
            $direccionId = $this->direccionSeleccionadaId;
        } elseif ($this->guardarDireccion) {
            $dir = ClienteDireccion::create([
                'cliente_id' => $cliente->id,
                'alias' => 'Dirección ' . ($cliente->direcciones()->count() + 1),
                'conjunto' => $this->conjunto,
                'torre' => $this->torre,
                'apto' => $this->apto,
                'es_principal' => $cliente->direcciones()->count() === 0,
            ]);
            $direccionId = $dir->id;
        }

        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'cliente_direccion_id' => $direccionId,
            'subtotal' => $this->subtotal,
            'descuento_puntos' => $this->descuentoPuntos,
            'cupon_descuento_id' => $this->cuponAplicado?->id,
            'descuento_cupon' => $this->descuentoCupon,
            'con_descuento_producto' => $this->hayDescuentosProducto,
            'total' => $this->total,
            'origen' => 'web',
            'estado' => 'pendiente_pago',
            'metodo_pago' => $this->metodoPago,
            'notas' => $this->notas,
        ]);

        foreach ($this->items as $item) {
            PedidoProducto::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $item['id'],
                'variant_id' => $item['variant_id'] ?? null,
                'variant_tamanio' => $item['variant_tamanio'] ?? null,
                'mitades' => $item['mitades'] ?? null,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'subtotal' => $item['precio'] * $item['cantidad'],
            ]);
        }

        Pago::create([
            'pedido_id' => $pedido->id,
            'monto' => $this->total,
            'metodo' => $this->metodoPago,
            'confirmado' => false,
            'fecha_pago' => now(),
        ]);

        // Increment coupon usage
        if ($this->cuponAplicado) {
            $this->cuponAplicado->increment('usos_actuales');
        }

        if ($this->descuentoPuntos > 0 && $this->recompensaAplicada) {
            $puntosCanje = (int) $this->recompensaAplicada['puntos'];
            Punto::create([
                'cliente_id' => $cliente->id,
                'puntos' => -$puntosCanje,
                'concepto' => "Canje: {$this->recompensaAplicada['valor']}" . ($this->recompensaAplicada['tipo'] === 'porcentaje' ? '%' : '$') . " dto Pedido #{$pedido->numero_pedido}",
                'pedido_id' => $pedido->id,
            ]);
            $cliente->decrement('puntos_acumulados', $puntosCanje);
        }

        session()->forget('carrito');
        $this->pedidoCreado = true;
        $this->pedidoId = $pedido->numero_pedido;

        $this->whatsappUrl = $this->generarWhatsAppUrl($pedido, $cliente);
        $this->whatsappComprobanteUrl = 'https://wa.me/573106444759?text=' . rawurlencode('Hola, aquí está mi comprobante de pago del pedido #' . $pedido->numero_pedido . '.');
    }

    private function generarWhatsAppUrl(Pedido $pedido, Cliente $cliente): string
    {
        $settings = NegocioSetting::getSettings();
        $telefonoTienda = '573106444759';
        $formato = fn($v) => '$ ' . number_format($v, 0, ',', '.');

        $lineas = [];
        $lineas[] = 'Vengo de https://diegospizzabq.click';
        $lineas[] = 'CO-' . $pedido->numero_pedido;
        $lineas[] = '🗓️ ' . $pedido->created_at->setTimezone('America/Bogota')->format('d/m/Y') . ' ⏰ ' . $pedido->created_at->setTimezone('America/Bogota')->format('h:i a');
        $lineas[] = '';
        $lineas[] = 'Tipo de servicio: Domicilio';
        $lineas[] = '';
        $lineas[] = 'Nombre: ' . $cliente->nombre;
        $lineas[] = 'Teléfono: 57 ' . $cliente->telefono;
        $lineas[] = 'Dirección: ' . $pedido->direccion_completa;
        $lineas[] = '';
        $lineas[] = '📝 Productos';
        foreach ($this->items as $item) {
            $mitades = !empty($item['mitades']) ? ' [' . collect($item['mitades'])->pluck('nombre')->implode(' / ') . ']' : '';
            $variant = !empty($item['variant_tamanio']) && empty($item['mitades']) ? ' (' . $item['variant_tamanio'] . ')' : '';
            $lineas[] = 'X' . $item['cantidad'] . ' ' . $item['nombre'] . $variant . $mitades . '  ' . $formato($item['precio'] * $item['cantidad']);
        }
        $lineas[] = '';
        $lineas[] = 'Subtotal: ' . $formato($pedido->subtotal);
        $lineas[] = 'Entrega: Por definir';
        $lineas[] = 'Total: ' . $formato($pedido->total);
        $lineas[] = '';
        $lineas[] = '💲 Pago';
        $lineas[] = 'Estado del pago: No pagado';
        $lineas[] = 'Total a pagar: ' . $formato($pedido->total);
        $lineas[] = 'Transferencia ' . $formato($pedido->total);
        $lineas[] = 'Llave ' . ($settings->llave ?? '3017226095');
        $lineas[] = 'Daviplata ' . ($settings->daviplata ?? '3007890081');
        $lineas[] = 'Nequi ' . ($settings->nequi ?? '3007890081');
        $lineas[] = '';
        $lineas[] = '👆 Envíanos este mensaje ahora. En cuanto lo recibamos estaremos atendiéndole.';

        return 'https://wa.me/' . $telefonoTienda . '?text=' . rawurlencode(implode("\n", $lineas));
    }

    #[Layout('layouts.store')]
    #[Title('Finalizar Pedido - Pizza Delivery')]
    public function render()
    {
        return view('livewire.checkout');
    }
}
