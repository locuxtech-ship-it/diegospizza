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
}
