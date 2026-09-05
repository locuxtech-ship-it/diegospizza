<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class CreateTenant extends Command
{
    protected $signature = 'tenant:create
        {dominio : Dominio completo del tenant (ej: diegospizza.local)}
        {nombre_negocio : Nombre del negocio}
        {--db-name= : Nombre de la DB (opcional, default: slug del dominio)}';

    protected $description = 'Crear un nuevo tenant con su base de datos';

    public function handle(): int
    {
        $dominio = $this->argument('dominio');
        $nombre = $this->argument('nombre_negocio');

        if (Tenant::where('dominio', $dominio)->exists()) {
            $this->error("El dominio '{$dominio}' ya existe");
            return static::FAILURE;
        }

        $dbName = $this->option('db-name') ?: Str::slug(explode('.', $dominio)[0]);

        $service = app(TenantService::class);

        $originalDefault = config('database.default');

        if (config('database.connections.landlord.driver') !== 'sqlite') {
            $this->createMysqlDatabase($dbName);
        }

        $tenant = $service->create($dominio, $nombre, $dbName);

        Config::set('database.default', $originalDefault);

        $this->info("Tenant '{$dominio}' creado exitosamente (id: {$tenant->id})");

        return static::SUCCESS;
    }

    private function createMysqlDatabase(string $dbName): void
    {
        try {
            $statement = "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            \Illuminate\Support\Facades\DB::connection('landlord')->statement($statement);
            $this->info("Base de datos '{$dbName}' creada");
        } catch (\Exception $e) {
            $this->warn("No se pudo crear la DB MySQL: {$e->getMessage()}");
        }
    }
}
