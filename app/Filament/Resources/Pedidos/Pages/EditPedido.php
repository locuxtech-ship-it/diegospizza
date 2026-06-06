<?php

namespace App\Filament\Resources\Pedidos\Pages;

use App\Filament\Pages\Comandas;
use App\Filament\Resources\Pedidos\PedidoResource;
use App\Models\NegocioSetting;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\Producto;
use App\Models\ProductoVariant;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPedido extends EditRecord
{
    protected static string $resource = PedidoResource::class;

    public function getView(): string
    {
        return 'filament.resources.pedidos.pages.edit-pedido';
    }

    public $readOnly = false;

    // Product management
    public $productosPedido = [];
    public $nuevoProductoId = null;
    public $nuevoVariantId = null;
    public $nuevoMitad1 = null;
    public $nuevoMitad2 = null;
    public $saboresPizza = [];

    // Payment management
    public $pagoMetodo = 'efectivo';
    public $pagoMonto = 0;
    public $pagoReferencia = '';
    public $pagosRegistrados = [];
    public $totalPagado = 0;
    public $totalPedido = 0;

    // Discount
    public $descuentoTipo = 'fijo';
    public $descuentoValor = 0;
    public $descuentoAplicado = 0;
    public $totalConDescuento = 0;

    // Client editing
    public $clienteNombre = '';
    public $clienteTelefono = '';
    public $clienteConjunto = '';
    public $clienteTorre = '';
    public $clienteApto = '';
    public $clienteDireccion = '';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->readOnly = in_array($this->getRecord()->estado, ['finalizado', 'cancelado']);

        if ($this->readOnly) {
            Notification::make()
                ->title($this->getRecord()->estado === 'finalizado' ? 'Pedido finalizado — solo lectura' : 'Pedido cancelado')
                ->warning()
                ->send();
        }

        $this->cargarProductos();
        $this->totalPedido = (float) $this->getRecord()->total;
        $this->totalConDescuento = $this->totalPedido;
        $this->cargarPagos();
        $this->cargarCliente();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => !$this->readOnly),
        ];
    }

    protected function getFormActions(): array
    {
        if ($this->readOnly) {
            return [
                Action::make('volver')
                    ->label('Volver a PDV')
                    ->icon('heroicon-o-arrow-left')
                    ->action(fn () => $this->redirect(Comandas::getUrl())),
            ];
        }

        $pagoCompleto = $this->totalPagado >= $this->totalConDescuento && $this->totalConDescuento > 0;

        if ($pagoCompleto) {
            return [
                Action::make('finalizar')
                    ->label('🎉 Finalizar Pedido')
                    ->action('finalizarPedido'),
            ];
        }

        return [
            Action::make('save')
                ->label('💾 Guardar')
                ->action('saveAndRedirect'),
        ];
    }

    // Product methods

    private function cargarProductos(): void
    {
        $this->productosPedido = PedidoProducto::with('producto')
            ->where('pedido_id', $this->getRecord()->id)
            ->get()
            ->toArray();
    }

    public function agregarProducto(): void
    {
        if ($this->readOnly || !$this->nuevoProductoId) return;

        $producto = Producto::with('variants')->find($this->nuevoProductoId);
        if (!$producto) return;

        if ($producto->es_personalizable) {
            $this->agregarMitadYMitadProducto($producto);
            return;
        }

        $variantId = $this->nuevoVariantId;
        $variantTamanio = null;
        $precio = (float) $producto->precio;

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

        foreach ($this->productosPedido as &$item) {
            if ($item['producto_id'] == $producto->id && ($item['variant_id'] ?? null) == $variantId) {
                $item['cantidad'] = (int) ($item['cantidad'] ?? 1) + 1;
                $item['subtotal'] = round($item['cantidad'] * (float) $item['precio_unitario'], 0, ',', '.');
                $this->nuevoProductoId = null;
                $this->nuevoVariantId = null;
                $this->recalcularSubtotalForm();
                return;
            }
        }

        $this->productosPedido[] = [
            'producto_id' => $producto->id,
            'variant_id' => $variantId,
            'variant_tamanio' => $variantTamanio,
            'nombre' => $producto->nombre,
            'precio_unitario' => $precio,
            'cantidad' => 1,
            'subtotal' => $precio,
        ];

        $this->nuevoProductoId = null;
        $this->nuevoVariantId = null;
        $this->recalcularSubtotalForm();
    }

    private function agregarMitadYMitadProducto(Producto $producto): void
    {
        if (!$this->nuevoMitad1 || !$this->nuevoMitad2) return;

        $sabor1 = Producto::find($this->nuevoMitad1);
        $sabor2 = Producto::find($this->nuevoMitad2);
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

        $this->productosPedido[] = [
            'producto_id' => $producto->id,
            'variant_id' => null,
            'variant_tamanio' => null,
            'nombre' => $producto->nombre . ' (Mitad y Mitad)',
            'precio_unitario' => $precio,
            'cantidad' => 1,
            'subtotal' => $precio,
            'mitades' => $mitades,
        ];

        $this->nuevoProductoId = null;
        $this->nuevoVariantId = null;
        $this->nuevoMitad1 = null;
        $this->nuevoMitad2 = null;
        $this->recalcularSubtotalForm();
    }

    public function cambiarCantidad(int $index, int $delta): void
    {
        if ($this->readOnly || !isset($this->productosPedido[$index])) return;

        $nueva = (int) ($this->productosPedido[$index]['cantidad'] ?? 1) + $delta;

        if ($nueva <= 0) {
            array_splice($this->productosPedido, $index, 1);
        } else {
            $this->productosPedido[$index]['cantidad'] = $nueva;
            $this->productosPedido[$index]['subtotal'] = round($nueva * (float) $this->productosPedido[$index]['precio_unitario'], 0, ',', '.');
        }

        $this->recalcularSubtotalForm();
    }

    public function quitarProducto(int $index): void
    {
        if ($this->readOnly || !isset($this->productosPedido[$index])) return;

        array_splice($this->productosPedido, $index, 1);
        $this->recalcularSubtotalForm();
    }

    private function recalcularSubtotalForm(): void
    {
        $subtotal = array_sum(array_map(fn ($i) => (float) ($i['subtotal'] ?? 0), $this->productosPedido));
        $this->data['subtotal'] = $subtotal;

        $record = $this->getRecord();

        if (auth()->user()?->isCajero()) {
            $this->data['descuento_manual'] = $record->descuento_manual ?? 0;
            $this->data['total'] = max(0, $subtotal - (float) ($record->descuento_puntos ?? 0) - (float) ($record->descuento_manual ?? 0));
        } else {
            $descuentoPuntos = (float) ($this->data['descuento_puntos'] ?? 0);
            $tipo = $this->data['descuento_manual_tipo'] ?? null;
            $valor = (float) ($this->data['descuento_manual_valor'] ?? 0);

            if ($tipo === 'porcentaje') {
                $descuentoManual = round($subtotal * $valor / 100, 0, ',', '.');
            } elseif ($tipo === 'monto') {
                $descuentoManual = $valor;
            } else {
                $descuentoManual = 0;
            }

            $this->data['descuento_manual'] = $descuentoManual;
            $this->data['total'] = max(0, $subtotal - $descuentoPuntos - $descuentoManual);
        }

        $this->totalPedido = (float) $this->data['total'];
        $this->actualizarDescuento();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->readOnly) {
            return $data;
        }

        if (($data['estado'] ?? '') === 'finalizado') {
            if ($this->getRecord()->estado !== 'entregado') {
                Notification::make()
                    ->title('Solo se puede finalizar desde "Ha Llegado"')
                    ->danger()
                    ->send();
                $data['estado'] = $this->getRecord()->estado;
                return $data;
            }

            $pagoCompleto = $this->totalPagado >= $this->totalConDescuento && $this->totalConDescuento > 0;
            if (!$pagoCompleto) {
                Notification::make()
                    ->title('No se puede finalizar: el pago no está completo')
                    ->danger()
                    ->send();
                $data['estado'] = $this->getRecord()->estado;
            }
        }

        $esCajero = auth()->user()?->isCajero();

        $subtotal = array_sum(array_map(fn ($i) => (float) ($i['subtotal'] ?? 0), $this->productosPedido));
        $data['subtotal'] = $subtotal;

        if ($esCajero) {
            unset($data['descuento_puntos'], $data['descuento_manual_tipo'], $data['descuento_manual_valor']);
            $data['descuento_manual'] = $this->getRecord()->descuento_manual ?? 0;
            $data['total'] = max(0, $subtotal - (float) ($this->getRecord()->descuento_puntos ?? 0) - (float) ($this->getRecord()->descuento_manual ?? 0));
            return $data;
        }

        $descuentoPuntos = (float) ($data['descuento_puntos'] ?? 0);
        $tipo = $data['descuento_manual_tipo'] ?? null;
        $valor = (float) ($data['descuento_manual_valor'] ?? 0);

        if ($tipo === 'porcentaje') {
            $data['descuento_manual'] = round($subtotal * $valor / 100, 0, ',', '.');
        } elseif ($tipo === 'monto') {
            $data['descuento_manual'] = $valor;
        } else {
            $data['descuento_manual'] = 0;
        }

        $data['total'] = max(0, $subtotal - $descuentoPuntos - $data['descuento_manual']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->readOnly) return;

        $pedidoId = $this->getRecord()->id;

        PedidoProducto::where('pedido_id', $pedidoId)->delete();

        foreach ($this->productosPedido as $item) {
            PedidoProducto::create([
                'pedido_id' => $pedidoId,
                'producto_id' => $item['producto_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'variant_tamanio' => $item['variant_tamanio'] ?? null,
                'mitades' => $item['mitades'] ?? null,
                'cantidad' => $item['cantidad'] ?? 1,
                'precio_unitario' => $item['precio_unitario'] ?? 0,
                'subtotal' => $item['subtotal'] ?? 0,
            ]);
        }
    }

    public function redirectToComandas(): void
    {
        $this->redirect(Comandas::getUrl());
    }

    private function guardarProductos(): void
    {
        $pedidoId = $this->getRecord()->id;
        PedidoProducto::where('pedido_id', $pedidoId)->delete();
        foreach ($this->productosPedido as $item) {
            PedidoProducto::create([
                'pedido_id' => $pedidoId,
                'producto_id' => $item['producto_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'variant_tamanio' => $item['variant_tamanio'] ?? null,
                'mitades' => $item['mitades'] ?? null,
                'cantidad' => $item['cantidad'] ?? 1,
                'precio_unitario' => $item['precio_unitario'] ?? 0,
                'subtotal' => $item['subtotal'] ?? 0,
            ]);
        }
    }

    public function saveAndRedirect(): void
    {
        if (($this->data['estado'] ?? '') === 'finalizado') {
            if ($this->getRecord()->estado !== 'entregado') {
                Notification::make()
                    ->title('Solo se puede finalizar desde "Ha Llegado"')
                    ->danger()
                    ->send();
                return;
            }

            $pagoCompleto = $this->totalPagado >= $this->totalConDescuento && $this->totalConDescuento > 0;
            if (!$pagoCompleto) {
                Notification::make()
                    ->title('No se puede finalizar: el pago no está completo')
                    ->danger()
                    ->send();
                return;
            }
        }

        $this->save(false, false);
        $this->guardarProductos();

        Notification::make()
            ->title("Pedido #{$this->getRecord()->numero_pedido} guardado")
            ->success()
            ->send();

        $this->redirect(Comandas::getUrl());
    }

    public function finalizarPedido(): void
    {
        if ($this->getRecord()->estado !== 'entregado') {
            Notification::make()
                ->title('Solo se puede finalizar desde "Ha Llegado"')
                ->danger()
                ->send();
            return;
        }

        $pagoCompleto = $this->totalPagado >= $this->totalConDescuento && $this->totalConDescuento > 0;
        if (!$pagoCompleto) {
            Notification::make()
                ->title('El pago no está completo')
                ->danger()
                ->send();
            return;
        }

        $this->save(false, false);
        $this->guardarProductos();

        $pedido = $this->getRecord();
        $pedido->update([
            'estado' => 'finalizado',
            'fecha_entrega' => now(),
        ]);

        Notification::make()
            ->title("Pedido #{$pedido->numero_pedido} finalizado")
            ->success()
            ->send();

        $this->redirect(Comandas::getUrl());
    }

    // Payment methods

    public function cargarPagos(): void
    {
        $this->pagosRegistrados = Pago::where('pedido_id', $this->getRecord()->id)
            ->where('confirmado', true)->get()->toArray();
        $this->totalPagado = (float) array_sum(array_column($this->pagosRegistrados, 'monto'));
    }

    public function registrarPago(): void
    {
        if ($this->readOnly) return;

        $this->validate([
            'pagoMonto' => 'required|numeric|min:1',
            'pagoMetodo' => 'required|in:' . implode(',', array_keys(NegocioSetting::getActivePaymentMethods())),
        ]);

        $pedido = $this->getRecord();

        // Apply discount if changed
        if ($this->descuentoAplicado > 0) {
            $pedido->update([
                'descuento_manual' => $this->descuentoAplicado,
                'descuento_manual_tipo' => $this->descuentoTipo,
                'descuento_manual_valor' => $this->descuentoValor,
            ]);
        }

        // Save client data
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
        $monto = min((float) $this->pagoMonto, $restante);

        Pago::create([
            'pedido_id' => $pedido->id,
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
                ->title("Pago registrado. Restan: $" . number_format($restante, 0, ',', '.'))
                ->success()
                ->send();
        }

        $this->dispatch('pedidoActualizado');

        $this->redirect(Comandas::getUrl());
    }

    public function eliminarPago(int $pagoId): void
    {
        if ($this->readOnly) return;

        $pago = Pago::find($pagoId);
        if (!$pago) return;

        $pago->delete();
        $this->cargarPagos();

        $metodosRestantes = array_unique(array_column($this->pagosRegistrados, 'metodo'));
        if (!empty($metodosRestantes)) {
            Pedido::find($this->getRecord()->id)->update([
                'metodo_pago' => count($metodosRestantes) > 1 ? 'mixto' : $metodosRestantes[0],
            ]);
        }

        Notification::make()
            ->title("Pago eliminado")
            ->success()
            ->send();
    }

    // Client methods
    private function cargarCliente(): void
    {
        $cliente = $this->getRecord()->cliente;
        if (!$cliente) return;
        $this->clienteNombre = $cliente->nombre ?? '';
        $this->clienteTelefono = $cliente->telefono ?? '';
        $this->clienteConjunto = $cliente->conjunto ?? '';
        $this->clienteTorre = $cliente->torre ?? '';
        $this->clienteApto = $cliente->apto ?? '';
        $this->clienteDireccion = $cliente ? collect(array_filter([$cliente->direccion, $cliente->conjunto, $cliente->torre ? "torre {$cliente->torre}" : null, $cliente->apto ? "apto {$cliente->apto}" : null]))->implode(', ') : '';
    }

    public function guardarCliente(): void
    {
        $cliente = $this->getRecord()->cliente;
        if (!$cliente) return;

        $cliente->update([
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

    // Discount methods
    public function updatedDescuentoTipo(): void
    {
        $this->actualizarDescuento();
    }

    public function updatedDescuentoValor(): void
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

    public function updatedNuevoProductoId($value): void
    {
        $this->nuevoVariantId = null;
        $this->nuevoMitad1 = null;
        $this->nuevoMitad2 = null;
        $this->saboresPizza = [];

        if (!$value) return;

        $producto = Producto::find($value);
        if ($producto && $producto->es_personalizable) {
            $this->saboresPizza = Producto::where('disponible', true)
                ->where('es_personalizable', false)
                ->where('id', '!=', $value)
                ->whereHas('categoria', fn ($q) => $q->where('es_pizza', true))
                ->orderBy('nombre')
                ->get();
        }
    }

    protected function getViewData(): array
    {
        $categorias = \App\Models\Categoria::where('activo', true)
            ->with('productosDisponibles')
            ->orderBy('orden')
            ->get();

        return [
            'pagosRegistrados' => $this->pagosRegistrados,
            'totalPagado' => $this->totalPagado,
            'totalPedido' => $this->totalPedido,
            'totalConDescuento' => $this->totalConDescuento,
            'descuentoAplicado' => $this->descuentoAplicado,
            'categorias' => $categorias,
            'readOnly' => $this->readOnly,
            'saboresPizza' => $this->saboresPizza,
        ];
    }
}
