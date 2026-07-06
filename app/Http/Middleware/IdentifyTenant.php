<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $this->extractTenantDomain($request);

        if (!$domain) {
            return $next($request);
        }

        $tenant = Tenant::where('dominio', $domain)->first();

        if (!$tenant || $tenant->estado !== 'activo') {
            abort(404, 'Tenant not found');
        }

        $this->configureTenantConnection($tenant);

        config(['database.default' => 'tenant']);

        app()->instance(Tenant::class, $tenant);

        return $next($request);
    }

    private function extractTenantDomain(Request $request): ?string
    {
        if ($request->header('X-Tenant-Domain')) {
            return $request->header('X-Tenant-Domain');
        }

        $host = $request->getHost();
        if (!in_array($host, ['localhost', '127.0.0.1', 'localhost:8000'])) {
            return $host;
        }

        return null;
    }

    private function configureTenantConnection(Tenant $tenant): void
    {
        $driver = config('database.default', 'sqlite');

        if ($driver === 'sqlite') {
            $dbPath = database_path("tenants/{$tenant->db_name}.sqlite");
            if (!file_exists($dbPath)) {
                touch($dbPath);
            }
            config(['database.connections.tenant' => [
                'driver' => 'sqlite',
                'database' => $dbPath,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ]]);
        } else {
            config(['database.connections.tenant' => [
                'driver' => $driver,
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => $tenant->db_name,
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ]]);
        }
    }
}
