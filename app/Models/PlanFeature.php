<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    protected $connection = 'landlord';

    public $timestamps = false;

    protected $fillable = [
        'plan_id',
        'feature_key',
        'enabled',
        'limits',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'limits' => 'array',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
