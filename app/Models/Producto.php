<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id',
        'nombre',
        'slug',
        'descripcion',
        'precio',
        'imagen',
        'ingredientes',
        'disponible',
        'es_personalizable',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'disponible' => 'boolean',
            'es_personalizable' => 'boolean',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductoVariant::class)->orderBy('orden');
    }

    public function getDescuentoActivoAttribute(): ?DescuentoProducto
    {
        return DescuentoProducto::descuentoParaProducto($this->id, $this->categoria_id);
    }

    public function getPrecioConDescuentoAttribute(): ?float
    {
        $desc = $this->descuento_activo;
        return $desc ? (float) $desc->calcularPrecio((float) $this->precio) : null;
    }

    public static function descuentosMap(): array
    {
        $activos = DescuentoProducto::where('activo', true)
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_expiracion', '>=', now())
            ->get();

        $porProducto = [];
        $porCategoria = [];
        foreach ($activos as $d) {
            if ($d->producto_id) {
                $porProducto[$d->producto_id] = $d;
            } elseif ($d->categoria_id) {
                $porCategoria[$d->categoria_id] = $d;
            }
        }

        return compact('porProducto', 'porCategoria');
    }
}
