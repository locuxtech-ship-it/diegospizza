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
        'repartidor_id',
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
        'en_camino_at',
        'entregado_at',
        'llegada_at',
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
            'en_camino_at' => 'datetime',
            'entregado_at' => 'datetime',
            'llegada_at' => 'datetime',
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

        static::updating(function ($pedido) {
            // Marca de tiempo de fases de entrega: un solo punto que cubre
            // cambiarEstado() (PDV/modal), PedidosTable y PedidoForm.
            if (!$pedido->isDirty('estado')) {
                return;
            }

            if ($pedido->estado === 'en_camino' && !$pedido->isDirty('en_camino_at')) {
                $pedido->en_camino_at = now();
            }

            if ($pedido->estado === 'entregado' && !$pedido->isDirty('entregado_at')) {
                $pedido->entregado_at = now();
            }
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

        $cliente = $this->cliente;
        if (!$cliente || empty($cliente->telefono)) {
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', $cliente->telefono);
        if (strlen($phone) < 10) {
            return;
        }

        $chatId = "57{$phone}@c.us";

        // Send estado notification
        $notifications = $chatbot['notifications'] ?? [];
        $message = $notifications[$estado] ?? '';

        if (!empty($message)) {
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

        // Send review link when finalized
        if ($estado === 'finalizado' && ($chatbot['review_enabled'] ?? false)) {
            $reviewMsg = $chatbot['review_message'] ?? '';
            if (!empty($reviewMsg)) {
                $link = route('review.form', ['pedido' => $this->id]);
                $reviewMsg = str_replace(
                    ['{numero}', '{nombre}', '{link}'],
                    [$this->numero_pedido, $cliente->nombre, $link],
                    $reviewMsg
                );

                try {
                    app(WhatsAppService::class)->sendText($chatId, $reviewMsg);
                } catch (\Exception $e) {
                    Log::error('WhatsApp review link failed', [
                        'pedido_id' => $this->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'repartidor_id');
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
