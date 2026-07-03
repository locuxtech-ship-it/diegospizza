<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteDireccion extends Model
{
    protected $table = 'cliente_direcciones';

    protected $fillable = [
        'cliente_id',
        'alias',
        'conjunto',
        'torre',
        'apto',
        'es_principal',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function getDireccionCompletaAttribute(): string
    {
        $parts = array_filter([$this->conjunto, $this->torre, $this->apto]);
        return implode(', ', $parts);
    }
}
