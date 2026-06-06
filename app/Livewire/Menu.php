<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\NegocioSetting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Menu extends Component
{
    public $categoriaId = null;
    public $negocio = null;
    public $estaAbierto = false;
    public $selectedProductId = null;
    public $selectedProduct = null;

    public $saboresPizza = [];
    public $mitad1 = null;
    public $mitad2 = null;

    public function mount()
    {
        $this->negocio = NegocioSetting::getSettings();
        $this->estaAbierto = NegocioSetting::isOpen();
    }

    public function filtrar($categoriaId)
    {
        $this->categoriaId = $categoriaId;
    }

    public function seleccionarProducto($productoId)
    {
        $this->selectedProduct = \App\Models\Producto::with('variants')->find($productoId);
        $this->selectedProductId = $productoId;
        $this->mitad1 = null;
        $this->mitad2 = null;

        if ($this->selectedProduct && $this->selectedProduct->es_personalizable) {
            $this->saboresPizza = \App\Models\Producto::where('disponible', true)
                ->where('es_personalizable', false)
                ->where('id', '!=', $productoId)
                ->whereHas('categoria', fn ($q) => $q->where('es_pizza', true))
                ->orderBy('nombre')
                ->get();
        }
    }

    public function cerrarVariantes()
    {
        $this->selectedProductId = null;
        $this->selectedProduct = null;
        $this->saboresPizza = [];
        $this->mitad1 = null;
        $this->mitad2 = null;
    }

    public function agregarConVariante($productoId, $variantId)
    {
        $this->dispatch('productoAgregado', productoId: $productoId, variantId: $variantId);
        $this->cerrarVariantes();
    }

    public function agregarMitadYMitad()
    {
        if (!$this->mitad1 || !$this->mitad2 || !$this->selectedProduct) return;

        $sabor1 = \App\Models\Producto::find($this->mitad1);
        $sabor2 = \App\Models\Producto::find($this->mitad2);
        if (!$sabor1 || !$sabor2) return;

        $vars1 = \App\Models\ProductoVariant::where('producto_id', $sabor1->id)->get();
        $vars2 = \App\Models\ProductoVariant::where('producto_id', $sabor2->id)->get();
        $mediana1 = $vars1->first(fn($v) => stripos($v->tamanio, 'mediana') !== false || stripos($v->tamanio, 'mediano') !== false);
        $mediana2 = $vars2->first(fn($v) => stripos($v->tamanio, 'mediana') !== false || stripos($v->tamanio, 'mediano') !== false);
        $precioSabor1 = (float) ($mediana1?->precio ?? $vars1->first()?->precio ?? $sabor1->precio);
        $precioSabor2 = (float) ($mediana2?->precio ?? $vars2->first()?->precio ?? $sabor2->precio);
        $precio = max($precioSabor1, $precioSabor2);

        $mitades = [
            ['producto_id' => $sabor1->id, 'nombre' => $sabor1->nombre, 'precio' => $precioSabor1],
            ['producto_id' => $sabor2->id, 'nombre' => $sabor2->nombre, 'precio' => $precioSabor2],
        ];

        $this->dispatch('productoAgregado',
            productoId: $this->selectedProduct->id,
            variantId: null,
            mitades: $mitades,
            precioOverride: $precio,
            nombreOverride: $this->selectedProduct->nombre . ' (Mitad y Mitad)'
        );
        $this->cerrarVariantes();
    }

    #[Layout('layouts.store')]
    #[Title('Menú - Pizza Delivery')]
    public function render()
    {
        $categorias = Categoria::where('activo', true)
            ->with(['productosDisponibles' => function ($query) {
                $query->with('variants');
            }])
            ->orderBy('orden')
            ->get();

        $categoriasFiltradas = $categorias;
        if ($this->categoriaId) {
            $categoriasFiltradas = $categorias->where('id', $this->categoriaId);
        }

        return view('livewire.menu', [
            'categorias' => $categorias,
            'categoriasFiltradas' => $categoriasFiltradas,
            'negocio' => $this->negocio,
            'estaAbierto' => $this->estaAbierto,
        ]);
    }
}
