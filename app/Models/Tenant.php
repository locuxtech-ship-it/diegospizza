<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $connection = 'landlord';

    protected $fillable = [
        'uuid',
        'dominio',
        'db_name',
        'nombre_negocio',
        'logo',
        'colores',
        'estado',
        'config',
    ];

    protected $casts = [
        'colores' => 'array',
        'config' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant) {
            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) Str::uuid();
            }
            if (empty($tenant->estado)) {
                $tenant->estado = 'activo';
            }
        });
    }
}
