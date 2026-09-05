<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;

class TenantSeedDemo extends Command
{
    protected $signature = 'tenant:seed-demo
        {dominio? : Dominio del tenant (si se omite, siembra todos)}';

    protected $description = 'Sembrar datos demo (categorías, productos, admin, clientes) en tenant(s)';

    public function handle(): int
    {
        $query = Tenant::query()->where('estado', 'activo');

        if ($dominio = $this->argument('dominio')) {
            $query->where('dominio', $dominio);
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->error('No hay tenants para sembrar');
            return static::FAILURE;
        }

        $service = app(TenantService::class);

        foreach ($tenants as $tenant) {
            $this->info("Sembrando tenant: {$tenant->dominio}...");

            $service->configureTenantConnection($tenant);

            if (!$service->seedDemo()) {
                $this->warn("  - ya tiene datos, se omite");
                continue;
            }

            $this->info("  - datos demo creados");
        }

        $this->info('Seed de tenants completado');
        return static::SUCCESS;
    }
}
