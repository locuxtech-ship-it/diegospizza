<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPayment extends Model
{
    protected $connection = 'landlord';

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'monto',
        'metodo',
        'referencia',
        'comprobante',
        'estado',
        'periodo',
        'notas',
        'pagado_en',
    ];

    protected $casts = [
        'monto' => 'integer',
        'pagado_en' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function aprobar(): void
    {
        $this->update(['estado' => 'aprobado', 'pagado_en' => now()]);
    }

    public function rechazar(): void
    {
        $this->update(['estado' => 'rechazado']);
    }
}
