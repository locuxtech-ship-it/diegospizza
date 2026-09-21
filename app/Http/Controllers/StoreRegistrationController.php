<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PlanService;
use App\Services\TenantService;
use Illuminate\Http\Request;

class StoreRegistrationController extends Controller
{
    public function show(string $planSlug = null)
    {
        $plans = Plan::where('activo', true)->orderBy('orden')->get();

        return view('store.register', [
            'plans' => $plans,
            'selectedPlan' => $planSlug ? Plan::bySlug($planSlug) : $plans->first(),
        ]);
    }

    public function store(Request $request, TenantService $tenants, PlanService $plans)
    {
        $data = $request->validate([
            'nombre_negocio' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:50'],
            'plan' => ['required', 'string', 'exists:landlord.plans,slug'],
            'email' => ['required', 'email', 'max:120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nombre_admin' => ['required', 'string', 'max:80'],
        ]);

        $dominio = $data['slug'] . '.facilo.app';

        if (Tenant::where('dominio', $dominio)->exists()) {
            return back()->withErrors(['slug' => 'Ese nombre de tienda ya está en uso.'])->withInput();
        }

        $tenant = $tenants->create($dominio, $data['nombre_negocio']);

        // Prueba gratuita de 30 días con el plan Negocio (sienten el valor completo).
        $plans->startTrial($tenant, 'negocio', 30);

        // Guardar la preferencia de plan elegida en el formulario para cuando pague.
        $config = $tenant->config ?? [];
        $config['plan_elegido'] = $data['plan'];
        $config['admin_email'] = $data['email'];
        $tenant->config = $config;
        $tenant->save();

        $admin = User::create([
            'name' => $data['nombre_admin'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'admin',
        ]);
        $config['admin_user_id'] = $admin->id;
        $tenant->config = $config;
        $tenant->save();

        return redirect()
            ->route('store.onboarding', [$tenant->uuid])
            ->with('success', '¡Tu tienda se creó correctamente!');
    }
}
