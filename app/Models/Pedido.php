<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $fillable = [
        'cliente_id',
        'numero_pedido',
        'subtotal',
        'descuento_puntos',
        'descuento_manual',
        'descuento_manual_tipo',
        'descuento_manual_valor',
        'total',
        'estado',
        'origen',
        'metodo_pago',
        'notas',
        'motivo_cancelacion',
        'fecha_entrega',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'descuento_puntos' => 'decimal:2',
            'descuento_manual' => 'decimal:2',
            'descuento_manual_valor' => 'decimal:2',
            'total' => 'decimal:2',
            'numero_pedido' => 'integer',
            'fecha_entrega' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($pedido) {
            if (!$pedido->numero_pedido) {
                $ultimo = static::whereDate('created_at', today())->max('numero_pedido');
                $pedido->numero_pedido = ($ultimo ?? 0) + 1;
            }
        });
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(PedidoProducto::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}
