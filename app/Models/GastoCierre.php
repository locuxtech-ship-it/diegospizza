<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GastoCierre extends Model
{
    protected $table = 'gastos_cierre';

    protected $fillable = ['cierre_id', 'descripcion', 'monto'];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
        ];
    }

    public function cierre(): BelongsTo
    {
        return $this->belongsTo(CierreCaja::class, 'cierre_id');
    }
}
