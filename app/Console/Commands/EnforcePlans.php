<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\Tenant;
use App\Services\PlanService;
use Illuminate\Console\Command;

class EnforcePlans extends Command
{
    protected $signature = 'tenant:enforce-plans';

    protected $description = 'Baja a Arranque los tenants cuya prueba gratuita expiró y no pagaron';

    public function handle(): int
    {
        $expired = Tenant::where('plan_status', 'trial')
            ->where('plan_expira', '<=', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Ninguna prueba expirada.');
            return static::SUCCESS;
        }

        $arranque = Plan::bySlug('arranque');
        $count = 0;

        foreach ($expired as $tenant) {
            $tenant->plan_id = $arranque?->id;
            $tenant->plan_status = 'activo';
            $tenant->plan_expira = null;
            $tenant->save();
            $count++;
            $this->info("Tenant {$tenant->dominio} → Arranque");
        }

        $this->info("Downgrade aplicado a {$count} tenant(s).");
        return static::SUCCESS;
    }
}
