<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;

class StoreController extends Controller
{
    public function menu()
    {
        $categorias = Categoria::where('activo', true)
            ->with('productosDisponibles')
            ->orderBy('orden')
            ->get();

        return view('store.menu', compact('categorias'));
    }
}
