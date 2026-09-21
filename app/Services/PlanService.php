<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;

class PlanService
{
    protected ?Tenant $tenant;

    public function __construct()
    {
        $this->tenant = app()->bound(Tenant::class) ? app(Tenant::class) : null;
    }

    /**
     * Cambiar el tenant sobre el que se evalúa el plan (útil en tests/CLI).
     */
    public function forTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * Plan del tenant actual (o null si no hay tenant).
     * Aplica la lógica de trial: si la prueba expiró, devuelve el plan Arranque.
     */
    public function currentPlan(): ?Plan
    {
        $tenant = $this->resolveTenant();

        if (!$tenant) {
            return null;
        }

        return $this->effectivePlan($tenant);
    }

    /**
     * Resolve el tenant actual de forma robusta: del container (middleware),
     * del host (subdominio) o fallback del primero activo.
     */
    protected function resolveTenant(): ?Tenant
    {
        if ($this->tenant) {
            return $this->tenant;
        }

        if (app()->bound(Tenant::class)) {
            $this->tenant = app(Tenant::class);
            return $this->tenant;
        }

        if (request()) {
            $host = request()->getHost();
            $firstPart = explode('.', $host)[0];
            if ($firstPart && !in_array($host, ['localhost', '127.0.0.1', 'localhost:8000'])) {
                $tenant = Tenant::where('dominio', $host)->with('plan')->first();
                if ($tenant) {
                    $this->tenant = $tenant;
                    return $tenant;
                }
            }
        }

        return null;
    }

    /**
     * ¿El tenant actual tiene habilitada la feature $key?
     * Si no hay tenant, devuelve false. Para features que son de base
     * (menú, PDV) se puede pasar $default como fallback.
     */
    public function can(string $key, bool $default = false): bool
    {
        $plan = $this->currentPlan();

        if (!$plan) {
            return $default;
        }

        return $plan->hasFeature($key);
    }

    /**
     * Iniciar prueba gratuita: asigna el plan $trialPlanSlug con estado 'trial'
     * y fecha de expiración en $dias días. Por defecto 30 días con el plan Negocio.
     */
    public function startTrial(Tenant $tenant, ?string $trialPlanSlug = 'negocio', int $dias = 30): Tenant
    {
        $tenant->plan_status = 'trial';
        $tenant->plan_expira = now()->addDays($dias);
        $tenant->plan_id = Plan::bySlug($trialPlanSlug)?->id;
        $tenant->save();

        return $tenant->load('plan');
    }

    /**
     * ¿La prueba gratuita del tenant ya expiró?
     */
    public function trialExpired(Tenant $tenant): bool
    {
        return $tenant->plan_status === 'trial'
            && $tenant->plan_expira
            && $tenant->plan_expira->isPast();
    }

    /**
     * Plan efectivo del tenant aplicando la lógica de trial:
     * si la prueba expiró y no pagó, baja automáticamente al plan Arranque.
     */
    public function effectivePlan(Tenant $tenant): ?Plan
    {
        if ($this->trialExpired($tenant)) {
            return Plan::bySlug('arranque');
        }

        return $tenant->plan ?? Plan::bySlug('arranque');
    }

    /**
     * Aplicar el downgrade si la prueba expiró. Idempotente:
     * solo actúa cuando la prueba venció y aún sigue en trial.
     */
    public function enforcePlan(Tenant $tenant): Tenant
    {
        if ($this->trialExpired($tenant)) {
            $tenant->plan_id = Plan::bySlug('arranque')?->id;
            $tenant->plan_status = 'activo';
            $tenant->plan_expira = null;
            $tenant->save();
        }

        return $tenant->load('plan');
    }

    /**
     * Límite numérico (si existe) para una feature del plan actual.
     */
    public function limit(string $key): mixed
    {
        $plan = $this->currentPlan();

        if (!$plan) {
            return null;
        }

        return $plan->limit($key);
    }

    /**
     * ¿Cuántos pedidos se han creado este mes para el tenant actual?
     */
    public function pedidosUsadosMes(): int
    {
        if (!app()->bound(\App\Models\Tenant::class)) {
            return 0;
        }

        $tenant = app(\App\Models\Tenant::class);
        $service = app(\App\Services\TenantService::class);
        $service->configureTenantConnection($tenant);

        return \App\Models\Pedido::where('created_at', '>=', now()->startOfMonth())
            ->count();
    }

    /**
     * ¿El tenant actual puede crear un pedido nuevo?
     * Respeta el límite max_pedidos_mes si existe en el plan efectivo.
     */
    public function canCreateOrder(): bool
    {
        $plan = $this->currentPlan();

        if (!$plan) {
            return true;
        }

        $max = $plan->limit('pedidos');

        if ($max === null || (int) $max <= 0) {
            return true;
        }

        return $this->pedidosUsadosMes() < (int) $max;
    }

    /**
     * Asignar (o cambiar) el plan de un tenant.
     */
    public function changePlan(Tenant $tenant, string|int $plan, ?string $status = 'activo', ?string $expira = null): Tenant
    {
        $model = is_numeric($plan)
            ? Plan::find($plan)
            : Plan::bySlug($plan);

        if (!$model) {
            throw new \InvalidArgumentException("Plan no encontrado: {$plan}");
        }

        $tenant->plan_id = $model->id;
        $tenant->plan_status = $status;
        $tenant->plan_expira = $expira ? \Carbon\Carbon::parse($expira) : $tenant->plan_expira;
        $tenant->save();

        return $tenant->load('plan');
    }

    /**
     * Verificar que el tenant no exceda el límite de usuarios de su plan.
     */
    public function canAddUser(Tenant $tenant, int $currentUsers): bool
    {
        $plan = $tenant->plan;

        if (!$plan) {
            return false;
        }

        if ((int) $plan->max_usuarios === 0) {
            return true;
        }

        return $currentUsers < (int) $plan->max_usuarios;
    }

    /**
     * Features habilitadas del plan actual (para la página "Mi Plan").
     */
    public function enabledFeatures(): array
    {
        $plan = $this->currentPlan();

        if (!$plan) {
            return [];
        }

        return $plan->features()
            ->where('enabled', true)
            ->pluck('feature_key')
            ->toArray();
    }
}
