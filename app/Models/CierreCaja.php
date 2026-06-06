<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CierreCaja extends Model
{
    protected $table = 'cierres_caja';

    protected $fillable = [
        'fecha', 'user_id', 'total_efectivo', 'total_transferencias', 'total_tarjeta',
        'total_ventas', 'total_gastos', 'efectivo_esperado', 'efectivo_real',
        'diferencia', 'observaciones', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'total_efectivo' => 'decimal:2',
            'total_transferencias' => 'decimal:2',
            'total_tarjeta' => 'decimal:2',
            'total_ventas' => 'decimal:2',
            'total_gastos' => 'decimal:2',
            'efectivo_esperado' => 'decimal:2',
            'efectivo_real' => 'decimal:2',
            'diferencia' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(GastoCierre::class, 'cierre_id');
    }
}
