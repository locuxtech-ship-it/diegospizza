<?php

namespace App\Models;

use App\Models\NegocioSetting;
use App\Services\WhatsAppService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Pedido extends Model
{
    protected $fillable = [
        'cliente_id',
        'numero_pedido',
        'subtotal',
        'descuento_puntos',
        'descuento_manual',
        'descuento_manual_tipo',
        'descuento_manual_valor',
        'total',
        'estado',
        'origen',
        'metodo_pago',
        'notas',
        'motivo_cancelacion',
        'fecha_entrega',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'descuento_puntos' => 'decimal:2',
            'descuento_manual' => 'decimal:2',
            'descuento_manual_valor' => 'decimal:2',
            'total' => 'decimal:2',
            'numero_pedido' => 'integer',
            'fecha_entrega' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($pedido) {
            if (!$pedido->numero_pedido) {
                $ultimo = static::whereDate('created_at', today())->max('numero_pedido');
                $pedido->numero_pedido = ($ultimo ?? 0) + 1;
            }
        });

        static::created(function ($pedido) {
            $pedido->sendNotification('pendiente_pago');
        });

        static::saved(function ($pedido) {
            $original = $pedido->getOriginal('estado');
            if ($original !== null && $original !== $pedido->estado) {
                $pedido->sendNotification($pedido->estado);
            }
        });
    }

    public function sendNotification(string $estado): void
    {
        $settings = NegocioSetting::getSettings();
        $chatbot = $settings->chatbot_settings ?? [];

        if (!($chatbot['order_notifications'] ?? false)) {
            return;
        }

        $notifications = $chatbot['notifications'] ?? [];
        $message = $notifications[$estado] ?? '';

        if (empty($message)) {
            return;
        }

        $cliente = $this->cliente;
        if (!$cliente || empty($cliente->telefono)) {
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', $cliente->telefono);
        if (strlen($phone) < 10) {
            return;
        }

        $chatId = "57{$phone}@c.us";

        $message = str_replace(
            ['{numero}', '{nombre}'],
            [$this->numero_pedido, $cliente->nombre],
            $message
        );

        try {
            app(WhatsAppService::class)->sendText($chatId, $message);
        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'pedido_id' => $this->id,
                'estado' => $estado,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos(): HasMany
    {
        return $this->hasMany(PedidoProducto::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}
