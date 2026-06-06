<?php

namespace App\Livewire;

use App\Models\Producto;
use App\Models\ProductoVariant;
use Livewire\Component;

class Cart extends Component
{
    public $items = [];
    public $total = 0;
    public $subtotal = 0;
    public $cantidad = 0;
    public $open = false;

    protected $listeners = [
        'productoAgregado' => 'agregarProducto',
        'toggleCart' => 'toggle',
    ];

    public function mount()
    {
        $this->cargarCarrito();
    }

    public function cargarCarrito()
    {
        $this->items = session()->get('carrito', []);
        $this->calcularTotales();
    }

    public function toggle()
    {
        $this->open = !$this->open;
    }

    public function abrir()
    {
        $this->open = true;
    }

    public function cerrar()
    {
        $this->open = false;
    }

    public function agregarProducto($productoId, $variantId = null, $mitades = null, $precioOverride = null, $nombreOverride = null)
    {
        $producto = Producto::findOrFail($productoId);

        $key = $variantId ? $productoId . '_' . $variantId : (string) $productoId;
        if ($mitades) {
            $key .= '_' . md5(json_encode($mitades));
        }
        $precio = $producto->precio;
        $variantTamanio = null;
        $nombre = $nombreOverride ?? $producto->nombre;

        if ($variantId) {
            $variant = ProductoVariant::findOrFail($variantId);
            $precio = $variant->precio;
            $variantTamanio = $variant->tamanio;
        }

        if ($precioOverride !== null) {
            $precio = (float) $precioOverride;
        }

        if (isset($this->items[$key])) {
            $this->items[$key]['cantidad']++;
        } else {
            $this->items[$key] = [
                'id' => $producto->id,
                'nombre' => $nombre,
                'variant_id' => $variantId,
                'variant_tamanio' => $variantTamanio,
                'precio' => $precio,
                'cantidad' => 1,
                'imagen' => $producto->imagen,
                'mitades' => $mitades,
            ];
        }

        session()->put('carrito', $this->items);
        $this->calcularTotales();
        $this->open = true;
        $this->dispatch('carritoActualizado');
    }

    public function actualizarCantidad($key, $cantidad)
    {
        if ($cantidad <= 0) {
            $this->eliminarProducto($key);
            return;
        }

        if (isset($this->items[$key])) {
            $this->items[$key]['cantidad'] = $cantidad;
        }

        session()->put('carrito', $this->items);
        $this->calcularTotales();
        $this->dispatch('carritoActualizado');
    }

    public function eliminarProducto($key)
    {
        unset($this->items[$key]);
        session()->put('carrito', $this->items);
        $this->calcularTotales();
        $this->dispatch('carritoActualizado');

        if (empty($this->items)) {
            $this->open = false;
        }
    }

    public function vaciarCarrito()
    {
        $this->items = [];
        session()->forget('carrito');
        $this->calcularTotales();
        $this->open = false;
        $this->dispatch('carritoActualizado');
    }

    public function calcularTotales()
    {
        $this->cantidad = array_sum(array_column($this->items, 'cantidad'));
        $this->subtotal = array_sum(array_map(fn($item) => $item['precio'] * $item['cantidad'], $this->items));
        $this->total = $this->subtotal;
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
