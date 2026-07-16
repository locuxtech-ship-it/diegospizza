<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DescuentoProducto extends Model
{
    protected $fillable = [
        'tipo',
        'valor',
        'fecha_inicio',
        'fecha_expiracion',
        'activo',
        'producto_id',
        'categoria_id',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'fecha_inicio' => 'datetime',
            'fecha_expiracion' => 'datetime',
            'activo' => 'boolean',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function scopeActivos($query)
    {
        $now = now();
        return $query->where('activo', true)
            ->where('fecha_inicio', '<=', $now)
            ->where('fecha_expiracion', '>=', $now);
    }

    public function calcularPrecio(float $precioOriginal): float
    {
        if ($this->tipo === 'porcentaje') {
            return max(0, $precioOriginal * (1 - $this->valor / 100));
        }
        return max(0, $precioOriginal - $this->valor);
    }

    public function getLabel(): string
    {
        if ($this->tipo === 'porcentaje') {
            return "-{$this->valor}%";
        }
        return '-$' . number_format((float) $this->valor, 0, ',', '.');
    }

    public static function descuentoParaProducto(int $productoId, ?int $categoriaId): ?self
    {
        $now = now();
        $directo = static::where('activo', true)
            ->where('producto_id', $productoId)
            ->where('fecha_inicio', '<=', $now)
            ->where('fecha_expiracion', '>=', $now)
            ->first();
        if ($directo) return $directo;

        if ($categoriaId) {
            return static::where('activo', true)
                ->whereNull('producto_id')
                ->where('categoria_id', $categoriaId)
                ->where('fecha_inicio', '<=', $now)
                ->where('fecha_expiracion', '>=', $now)
                ->first();
        }

        return null;
    }
}
