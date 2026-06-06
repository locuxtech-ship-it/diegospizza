<?php

namespace App\Http\Controllers;

use App\Models\NegocioSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function saveConfig(Request $request)
    {
        $negocio = NegocioSetting::first() ?? NegocioSetting::create();
        
        $data = $request->validate([
            'nombre_negocio' => 'required',
            'telefono' => 'nullable',
            'direccion' => 'nullable',
            'horario_apertura' => 'required',
            'horario_cierre' => 'required',
            'puntos_por_dolar' => 'required|numeric|min:1',
            'descuento_por_punto' => 'required|numeric|min:0',
            'impresora_nombre' => 'nullable',
            'llave' => 'nullable',
            'daviplata' => 'nullable',
            'nequi' => 'nullable',
            'ticket_size' => 'nullable|in:57,80',
        ]);

        if ($request->hasFile('logo')) {
            if ($negocio->logo) Storage::disk('public')->delete($negocio->logo);
            $data['logo'] = $request->file('logo')->store('negocio', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($negocio->banner) Storage::disk('public')->delete($negocio->banner);
            $data['banner'] = $request->file('banner')->store('negocio', 'public');
        }

        $data['dias_laborales'] = $request->input('dias_laborales', []);
        $data['imprimir_automaticamente'] = $request->has('imprimir_automaticamente');

        $negocio->update($data);

        return back()->with('success', 'Configuración guardada correctamente');
    }
}