<?php

namespace App\Console\Commands;

use App\Models\NegocioSetting;
use App\Models\Tenant;
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

        $tenant = Tenant::create([
            'dominio' => $dominio,
            'nombre_negocio' => $nombre,
            'db_name' => $dbName,
        ]);

        $this->info("Tenant creado en landlord DB (id: {$tenant->id})");

        $this->configureTenantConnection($tenant);

        if (config('database.connections.tenant.driver') !== 'sqlite') {
            $this->createMysqlDatabase($dbName);
        }

        $originalDefault = config('database.default');
        Config::set('database.default', 'tenant');

        $this->info('Ejecutando migraciones...');
        $this->call('migrate', ['--force' => true]);

        $this->seedDefaults($nombre);

        Config::set('database.default', $originalDefault);

        $this->info("Tenant '{$dominio}' creado exitosamente");

        return static::SUCCESS;
    }

    private function configureTenantConnection(Tenant $tenant): void
    {
        $driver = config('database.default', 'sqlite');

        if ($driver === 'sqlite') {
            $dbPath = database_path("tenants/{$tenant->db_name}.sqlite");
            if (!file_exists($dbPath)) {
                touch($dbPath);
            }
            Config::set('database.connections.tenant', [
                'driver' => 'sqlite',
                'database' => $dbPath,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ]);
        } else {
            Config::set('database.connections.tenant', [
                'driver' => $driver,
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => $tenant->db_name,
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ]);
        }
    }

    private function createMysqlDatabase(string $dbName): void
    {
        try {
            Config::set('database.connections.landlord.host', env('DB_HOST', '127.0.0.1'));
            $statement = "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            \Illuminate\Support\Facades\DB::connection('landlord')->statement($statement);
            $this->info("Base de datos '{$dbName}' creada");
        } catch (\Exception $e) {
            $this->warn("No se pudo crear la DB MySQL: {$e->getMessage()}");
        }
    }

    private function seedDefaults(string $nombre): void
    {
        NegocioSetting::create([
            'nombre_negocio' => $nombre,
            'metodos_pago_activos' => ['efectivo', 'tarjeta', 'transferencia'],
            'puntos_ganancia_monto' => 10000,
            'puntos_ganancia_valor' => 1,
        ]);
        $this->info('Configuración por defecto creada');
    }
}
