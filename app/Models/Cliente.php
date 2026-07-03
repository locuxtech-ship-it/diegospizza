<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $appends = ['clasificacion', 'clasificacion_label'];

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'conjunto',
        'torre',
        'apto',
        'email',
        'notas',
        'puntos_acumulados',
    ];

    public function getDireccionCompletaAttribute(): string
    {
        $parts = array_filter([$this->conjunto, $this->torre, $this->apto]);
        return implode(', ', $parts);
    }

    public function getClasificacionAttribute(): string
    {
        $total = $this->pedidos()->count();
        if ($total <= 1) {
            return 'nuevo';
        }

        $mes = $this->pedidos()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($mes >= 5) return 'elite';
        if ($mes >= 2) return 'frecuente';

        return 'nuevo';
    }

    public function getClasificacionLabelAttribute(): string
    {
        return match ($this->clasificacion) {
            'elite' => '⭐ Elite',
            'frecuente' => '🔥 Frecuente',
            default => '🆕 Nuevo',
        };
    }

    public function getClasificacionColorAttribute(): string
    {
        return match ($this->clasificacion) {
            'elite' => '#f59e0b',
            'frecuente' => '#ea580c',
            default => '#6b7280',
        };
    }

    public function getClasificacionBgAttribute(): string
    {
        return match ($this->clasificacion) {
            'elite' => '#fef3c7',
            'frecuente' => '#ffedd5',
            default => '#f3f4f6',
        };
    }

    public function direcciones(): HasMany
    {
        return $this->hasMany(ClienteDireccion::class);
    }

    public function direccionPrincipal(): ?ClienteDireccion
    {
        return $this->direcciones()->where('es_principal', true)->first()
            ?? $this->direcciones()->first();
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function puntos(): HasMany
    {
        return $this->hasMany(Punto::class);
    }
}
