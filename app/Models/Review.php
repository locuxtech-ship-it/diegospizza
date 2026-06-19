<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'pedido_id',
        'cliente_id',
        'nombre',
        'telefono',
        'rating',
        'comentario',
        'visible',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'visible' => 'boolean',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
