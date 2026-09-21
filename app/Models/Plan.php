<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plan extends Model
{
    protected $connection = 'landlord';

    protected $fillable = [
        'slug',
        'nombre',
        'precio_mensual',
        'max_usuarios',
        'activo',
        'descripcion',
        'orden',
    ];

    protected $casts = [
        'precio_mensual' => 'integer',
        'max_usuarios' => 'integer',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function hasFeature(string $key): bool
    {
        return $this->features()
            ->where('feature_key', $key)
            ->where('enabled', true)
            ->exists();
    }

    public function limit(string $key): mixed
    {
        $feature = $this->features()
            ->where('feature_key', $key)
            ->first();

        if (!$feature || empty($feature->limits)) {
            return null;
        }

        // Si la clave directa existe, la usamos.
        if (array_key_exists($key, $feature->limits)) {
            return $feature->limits[$key];
        }

        // Si no, devolvemos el primer valor numérico del array de límites.
        foreach ($feature->limits as $v) {
            if (is_numeric($v) || is_null($v)) {
                return $v;
            }
        }

        return null;
    }

    public static function bySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Etiquetas legibles de las features habilitadas (para la UI de planes).
     * Incluye el límite de pedidos mensuales como primera línea.
     */
    public function abilities(): array
    {
        $map = [
            'menu_digital' => 'Menú digital',
            'pedidos' => 'Pedidos en línea',
            'punto_venta' => 'Toma de pedidos (PDV)',
            'whatsapp_bot' => 'Bot de WhatsApp',
            'reportes' => 'Reportes',
            'fidelidad' => 'Fidelidad y puntos',
            'multi_usuario' => 'Varios usuarios',
            'cierre_caja' => 'Cierre de caja',
            'historial' => 'Historial de pedidos',
            'clientes' => 'Gestión de clientes',
            'resenas' => 'Reseñas de clientes',
            'exportar_csv' => 'Exportar datos (CSV)',
            'reporte_personalizado' => 'Reportes con rango personalizado',
        ];

        $labels = [];

        $maxPedidos = $this->limit('pedidos');
        if ($maxPedidos !== null && (int) $maxPedidos > 0) {
            $labels[] = (int) $maxPedidos . ' pedidos/mes';
        } else {
            $labels[] = 'Pedidos ilimitados';
        }

        $labels = array_merge($labels, $this->features()
            ->where('enabled', true)
            ->get()
            ->map(fn ($f) => $map[$f->feature_key] ?? ucfirst(str_replace('_', ' ', $f->feature_key)))
            ->values()
            ->toArray());

        return array_values(array_unique($labels));
    }
}
