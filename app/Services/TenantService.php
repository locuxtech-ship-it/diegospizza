<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\NegocioSetting;
use App\Models\Producto;
use App\Models\ProductoVariant;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Crear un tenant completo: registro en landlord DB + DB de tenant
     * + migraciones + configuración por defecto.
     */
    public function create(string $dominio, string $nombre, ?string $dbName = null): Tenant
    {
        if (Tenant::where('dominio', $dominio)->exists()) {
            throw new \InvalidArgumentException("El dominio '{$dominio}' ya existe");
        }

        $dbName = $dbName ?: Str::slug(explode('.', $dominio)[0]);

        $tenant = Tenant::create([
            'dominio' => $dominio,
            'nombre_negocio' => $nombre,
            'db_name' => $dbName,
        ]);

        $this->runMigrations($tenant);
        $this->seedDefaults($nombre);

        return $tenant;
    }

    /**
     * Configurar la conexión 'tenant' para un tenant dado y dejarla como default.
     * Usa DB::purge para evitar conexión cacheada entre tenants.
     */
    public function configureTenantConnection(Tenant $tenant): void
    {
        $driver = $this->detectDriver();

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

        DB::purge('tenant');
        Config::set('database.default', 'tenant');
    }

    /**
     * Ejecutar migraciones contra la DB del tenant.
     */
    public function runMigrations(Tenant $tenant, ?callable $notify = null): void
    {
        $this->configureTenantConnection($tenant);

        if ($notify) {
            $notify('Migrando base de datos...');
        }

        app('Illuminate\Contracts\Console\Kernel')->call('migrate', ['--force' => true]);
    }

    /**
     * Sembrar la configuración mínima del negocio para que la tienda funcione.
     */
    public function seedDefaults(string $nombre): void
    {
        NegocioSetting::create([
            'nombre_negocio' => $nombre,
            'metodos_pago_activos' => ['efectivo', 'tarjeta', 'transferencia'],
            'puntos_ganancia_monto' => 10000,
            'puntos_ganancia_valor' => 1,
        ]);
    }

    /**
     * Sembrar datos demo (categorías, productos, variantes, admin y clientes)
     * para poder probar el flujo completo. Idempotente: solo si no hay categorías.
     */
    public function seedDemo(): bool
    {
        $tieneCatalogo = Categoria::exists();

        if (!$tieneCatalogo) {
            $this->crearCatalogo();
        }

        if (!User::where('email', 'admin@diegospizza.com')->exists()) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@diegospizza.com',
                'password' => 'admin123',
                'role' => 'admin',
            ]);
        }

        if (!User::where('email', 'cajero@diegospizza.com')->exists()) {
            User::create([
                'name' => 'Cajero',
                'email' => 'cajero@diegospizza.com',
                'password' => 'admin123',
                'role' => 'cajero',
            ]);
        }

        if (!Cliente::where('telefono', '3101234567')->exists()) {
            Cliente::create([
                'nombre' => 'Cliente Demo',
                'telefono' => '3101234567',
                'direccion' => 'Conjunto Alameda, Torre 1, Apto 203',
                'conjunto' => 'Conjunto Alameda',
                'torre' => 'Torre 1',
                'apto' => 'Apto 203',
                'puntos_acumulados' => 50,
            ]);
        }

        return $tieneCatalogo;
    }

    private function crearCatalogo(): void
    {
        $pizzas = Categoria::create(['nombre' => 'Pizzas', 'slug' => 'pizzas', 'descripcion' => 'Pizza artesanal al horno de leña', 'orden' => 1, 'activo' => true, 'es_pizza' => true]);
        $hamburguesas = Categoria::create(['nombre' => 'Hamburguesas', 'slug' => 'hamburguesas', 'descripcion' => 'Hamburguesas con pan artesanal', 'orden' => 2, 'activo' => true]);
        $bebidas = Categoria::create(['nombre' => 'Bebidas', 'slug' => 'bebidas', 'descripcion' => 'Refrescos y jugos naturales', 'orden' => 3, 'activo' => true]);
        $postres = Categoria::create(['nombre' => 'Postres', 'slug' => 'postres', 'descripcion' => 'Dulces caseros', 'orden' => 4, 'activo' => true]);

        $this->crearPizza($pizzas, 'Pizza Margherita', 'Tomate, mozzarella fresca y albahaca', 'margherita', [['Mediana', 28000, 1], ['Familiar', 38000, 2]]);
        $this->crearPizza($pizzas, 'Pizza Pepperoni', 'Doble pepperoni con queso mozzarella', 'pepperoni', [['Mediana', 32000, 1], ['Familiar', 44000, 2]]);
        $this->crearPizza($pizzas, 'Pizza Hawaiana', 'Jamón, piña y queso mozzarella', 'hawaiana', [['Mediana', 30000, 1], ['Familiar', 42000, 2]]);
        $this->crearPizza($pizzas, 'Pizza Suprema', 'Pepperoni, champiñones, pimentón y aceitunas', 'suprema', [['Mediana', 36000, 1], ['Familiar', 48000, 2]]);

        Producto::create([
            'categoria_id' => $hamburguesas->id,
            'nombre' => 'Hamburguesa Clásica',
            'slug' => 'hamburguesa-clasica',
            'descripcion' => 'Carne 100% res, queso cheddar, lechuga y tomate',
            'precio' => 15000,
            'ingredientes' => 'Carne de res, queso cheddar, lechuga, tomate, salsa de la casa',
            'disponible' => true,
            'es_personalizable' => false,
            'orden' => 1,
        ]);
        Producto::create([
            'categoria_id' => $hamburguesas->id,
            'nombre' => 'Hamburguesa BBQ',
            'slug' => 'hamburguesa-bbq',
            'descripcion' => 'Doble carne, tocineta, cebolla caramelizada y salsa BBQ',
            'precio' => 19000,
            'ingredientes' => 'Doble carne, tocineta, cebolla caramelizada, salsa BBQ',
            'disponible' => true,
            'es_personalizable' => false,
            'orden' => 2,
        ]);

        Producto::create([
            'categoria_id' => $bebidas->id,
            'nombre' => 'Gaseosa Personal',
            'slug' => 'gaseosa-personal',
            'descripcion' => '350ml bien fría',
            'precio' => 3500,
            'disponible' => true,
            'es_personalizable' => false,
            'orden' => 1,
        ]);
        Producto::create([
            'categoria_id' => $bebidas->id,
            'nombre' => 'Limonada Natural',
            'slug' => 'limonada-natural',
            'descripcion' => 'Limón, hierbabuena y panela',
            'precio' => 5000,
            'disponible' => true,
            'es_personalizable' => false,
            'orden' => 2,
        ]);

        Producto::create([
            'categoria_id' => $postres->id,
            'nombre' => 'Brownie con Helado',
            'slug' => 'brownie-con-helado',
            'descripcion' => 'Brownie caliente con bola de helado de vainilla',
            'precio' => 8000,
            'disponible' => true,
            'es_personalizable' => false,
            'orden' => 1,
        ]);
    }

    private function crearPizza(Categoria $categoria, string $nombre, string $descripcion, string $slug, array $variantes): void
    {
        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'nombre' => $nombre,
            'slug' => $slug,
            'descripcion' => $descripcion,
            'precio' => $variantes[0][1],
            'disponible' => true,
            'es_personalizable' => true,
            'orden' => $categoria->productos()->count() + 1,
        ]);

        foreach ($variantes as [$tamanio, $precio, $orden]) {
            ProductoVariant::create([
                'producto_id' => $producto->id,
                'tamanio' => $tamanio,
                'precio' => $precio,
                'orden' => $orden,
            ]);
        }
    }

    private function detectDriver(): string
    {
        $default = config('database.default', 'sqlite');
        $driver = config("database.connections.{$default}.driver", $default);

        if ($driver === 'landlord') {
            return config('database.connections.landlord.driver', 'sqlite');
        }

        return $driver === 'tenant' ? env('DB_CONNECTION', 'sqlite') : $driver;
    }
}
