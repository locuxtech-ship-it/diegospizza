<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'orden',
        'activo',
        'es_pizza',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'es_pizza' => 'boolean',
        ];
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function productosDisponibles(): HasMany
    {
        return $this->hasMany(Producto::class)->where('disponible', true);
    }
}
