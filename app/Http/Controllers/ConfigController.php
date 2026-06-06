<?php

namespace App\Http\Controllers;

use App\Models\NegocioSetting;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function guardar(Request $request)
    {
        $s = NegocioSetting::getSettings();
        
        $s->nombre_negocio = $request->nombre_negocio;
        $s->telefono = $request->telefono;
        $s->direccion = $request->direccion;
        $s->horario_apertura = $request->horario_apertura;
        $s->horario_cierre = $request->horario_cierre;
        $s->dias_laborales = $request->dias_laborales ?? [];
        $s->puntos_por_dolar = $request->puntos_por_dolar;
        $s->descuento_por_punto = $request->descuento_por_punto;
        $s->imprimir_automaticamente = $request->has('imprimir_automaticamente');
        $s->impresora_nombre = $request->impresora_nombre;
        
        if ($request->hasFile('logo')) {
            $s->logo = $request->logo->store('negocio', 'public');
        }
        if ($request->hasFile('banner')) {
            $s->banner = $request->banner->store('negocio', 'public');
        }
        
        $s->save();
        
        return back()->with('success', 'Configuración guardada');
    }
}