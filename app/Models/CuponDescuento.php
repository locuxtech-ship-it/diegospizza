<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuponDescuento extends Model
{
    protected $table = 'cupones_descuento';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'monto_minimo',
        'usos_maximos',
        'usos_actuales',
        'por_cliente',
        'activo',
        'fecha_inicio',
        'fecha_expiracion',
        'cliente_id',
    ];

    public function setCodigoAttribute($value): void
    {
        $this->attributes['codigo'] = strtoupper($value);
    }

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'monto_minimo' => 'decimal:2',
            'usos_maximos' => 'integer',
            'usos_actuales' => 'integer',
            'por_cliente' => 'boolean',
            'activo' => 'boolean',
            'fecha_inicio' => 'datetime',
            'fecha_expiracion' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function esValido(?float $totalCarrito = null, ?int $clienteId = null): bool
    {
        if (!$this->activo) return false;
        if ($this->usos_maximos && $this->usos_actuales >= $this->usos_maximos) return false;
        if ($this->fecha_inicio && now()->lt($this->fecha_inicio)) return false;
        if ($this->fecha_expiracion && now()->gt($this->fecha_expiracion)) return false;
        if ($this->cliente_id && $clienteId && $this->cliente_id !== $clienteId) return false;
        if ($totalCarrito !== null && $this->monto_minimo && $totalCarrito < $this->monto_minimo) return false;
        if ($this->por_cliente && $clienteId) {
            $yaUsado = \App\Models\Pedido::where('cliente_id', $clienteId)
                ->where('cupon_descuento_id', $this->id)
                ->exists();
            if ($yaUsado) return false;
        }
        return true;
    }

    public function calcularDescuento(float $subtotal): float
    {
        if ($this->tipo === 'porcentaje') {
            return min($subtotal * $this->valor / 100, $subtotal);
        }
        return min($this->valor, $subtotal);
    }
}
