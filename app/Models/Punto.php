<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Punto extends Model
{
    protected $fillable = [
        'cliente_id',
        'puntos',
        'concepto',
        'pedido_id',
    ];

    protected function casts(): array
    {
        return [
            'puntos' => 'integer',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
