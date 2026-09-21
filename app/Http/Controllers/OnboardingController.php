<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show(string $uuid)
    {
        $tenant = Tenant::where('uuid', $uuid)->firstOrFail();

        $service = app(TenantService::class);
        $service->configureTenantConnection($tenant);

        $settings = \App\Models\NegocioSetting::getSettings();

        return view('store.onboarding', compact('tenant', 'settings'));
    }

    public function update(Request $request, string $uuid)
    {
        $tenant = Tenant::where('uuid', $uuid)->firstOrFail();

        $data = $request->validate([
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:200'],
        ]);

        $service = app(TenantService::class);
        $service->configureTenantConnection($tenant);

        $settings = \App\Models\NegocioSetting::getSettings();
        $settings->telefono = $data['telefono'] ?? $settings->telefono;
        $settings->direccion = $data['direccion'] ?? $settings->direccion;
        $settings->save();

        return redirect()->route('store.onboarding', $tenant->uuid)
            ->with('success', 'Información guardada. ¡Ya puedes empezar a vender!');
    }
}
