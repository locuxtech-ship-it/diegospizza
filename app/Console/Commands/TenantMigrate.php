<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;

class TenantMigrate extends Command
{
    protected $signature = 'tenant:migrate
        {dominio? : Dominio del tenant (si se omite, migra todos)}';

    protected $description = 'Aplicar migraciones pendientes a la(s) base(s) de datos de tenant(s)';

    public function handle(): int
    {
        $query = Tenant::query()->where('estado', 'activo');

        if ($dominio = $this->argument('dominio')) {
            $query->where('dominio', $dominio);
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->error('No hay tenants para migrar');
            return static::FAILURE;
        }

        $service = app(TenantService::class);

        foreach ($tenants as $tenant) {
            $this->info("Migrando tenant: {$tenant->dominio} ({$tenant->db_name})...");

            $service->runMigrations($tenant);
        }

        $this->info('Migraciones de tenants completadas');
        return static::SUCCESS;
    }
}
