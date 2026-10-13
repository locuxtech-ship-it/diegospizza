<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegocioSetting extends Model
{
    protected $fillable = [
        'nombre_negocio',
        'logo',
        'banner',
        'telefono',
        'direccion',
        'horario_apertura',
        'horario_cierre',
        'dias_laborales',
        'tipos_servicio',
        'imprimir_automaticamente',
        'impresora_nombre',
        'puntos_por_dolar',
        'descuento_por_punto',
        'puntos_ganancia_monto',
        'puntos_ganancia_valor',
        'puntos_recompensas',
        'llave',
        'daviplata',
        'nequi',
        'metodos_pago_activos',
        'ticket_size',
        'ticket_mostrar_logo',
        'ticket_escala',
        'ticket_interlineado',
        'ticket_espaciado',
        'ticket_negritas',
        'ticket_margen',
        'ticket_fuente',
        'horarios_por_dia',
        'chatbot_settings',
    ];

    protected function casts(): array
    {
        return [
            'chatbot_settings' => 'array',
            'dias_laborales' => 'array',
            'tipos_servicio' => 'array',
            'imprimir_automaticamente' => 'boolean',
            'ticket_mostrar_logo' => 'boolean',
            'ticket_escala' => 'integer',
            'ticket_interlineado' => 'string',
            'ticket_espaciado' => 'string',
            'ticket_negritas' => 'boolean',
            'puntos_por_dolar' => 'integer',
            'descuento_por_punto' => 'decimal:2',
            'metodos_pago_activos' => 'array',
            'puntos_ganancia_monto' => 'decimal:2',
            'puntos_ganancia_valor' => 'integer',
            'puntos_recompensas' => 'array',
            'horarios_por_dia' => 'array',
        ];
    }

    public static function getSettings(): self
    {
        return self::first() ?? self::create();
    }

    public static function getActivePaymentMethods(): array
    {
        $settings = self::getSettings();
        $activos = $settings->metodos_pago_activos ?? ['efectivo', 'tarjeta', 'transferencia'];
        $labels = [
            'efectivo' => ['label' => 'Efectivo', 'icon' => '💵'],
            'tarjeta' => ['label' => 'Tarjeta', 'icon' => '💳'],
            'transferencia' => ['label' => 'Transferencia', 'icon' => '🏦'],
        ];
        return array_intersect_key($labels, array_flip($activos));
    }

    public static function getTodayHours(): array
    {
        $settings = self::getSettings();
        $dayName = ucfirst(now()->locale('es')->dayName);
        $horarios = $settings->horarios_por_dia ?? [];

        if (isset($horarios[$dayName])) {
            return [
                'apertura' => $horarios[$dayName]['apertura'] ?? $settings->horario_apertura ?? '11:00',
                'cierre' => $horarios[$dayName]['cierre'] ?? $settings->horario_cierre ?? '23:00',
            ];
        }

        return [
            'apertura' => $settings->horario_apertura ?? '11:00',
            'cierre' => $settings->horario_cierre ?? '23:00',
        ];
    }

    public static function isOpen(): bool
    {
        $settings = self::getSettings();

        $now = now()->locale('es');
        $dayName = ucfirst($now->dayName);

        $diasLaborales = $settings->dias_laborales ?? ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        if (!in_array($dayName, $diasLaborales)) {
            return false;
        }

        $hoy = self::getTodayHours();
        $apertura = \Carbon\Carbon::parse($hoy['apertura']);
        $cierre = \Carbon\Carbon::parse($hoy['cierre']);
        $horaActual = $now->copy();

        if ($cierre < $apertura) {
            return $horaActual->gte($apertura) || $horaActual->lt($cierre);
        }

        return $horaActual->gte($apertura) && $horaActual->lt($cierre);
    }

    public static function formatHora(string $hora): string
    {
        $parts = explode(':', $hora);
        $h = (int) $parts[0];
        $m = $parts[1] ?? '00';
        $period = $h >= 12 ? 'PM' : 'AM';
        $h12 = $h % 12 ?: 12;
        return "{$h12}:{$m} {$period}";
    }

    public static function getNextOpening(): array
    {
        $settings = self::getSettings();
        $diasLaborales = $settings->dias_laborales ?? ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $dayName = ucfirst(now()->locale('es')->dayName);

        if (in_array($dayName, $diasLaborales)) {
            $hoy = self::getTodayHours();
            $apertura = \Carbon\Carbon::parse($hoy['apertura']);
            $cierre = \Carbon\Carbon::parse($hoy['cierre']);
            $horaActual = now();

            if ($cierre >= $apertura && $horaActual->lt($apertura)) {
                return ['cuando' => 'hoy', 'hora' => $hoy['apertura']];
            }
            if ($cierre < $apertura && $horaActual->gte($apertura) && $horaActual->lt($cierre)) {
                return ['cuando' => 'hoy', 'hora' => $hoy['apertura']];
            }
        }

        $horarios = $settings->horarios_por_dia ?? [];
        for ($i = 1; $i <= 7; $i++) {
            $future = now()->copy()->addDays($i);
            $futureDay = ucfirst($future->locale('es')->dayName);
            if (in_array($futureDay, $diasLaborales)) {
                $hora = $horarios[$futureDay]['apertura'] ?? $settings->horario_apertura ?? '11:00';
                return [
                    'cuando' => $i === 1 ? 'mañana' : $futureDay,
                    'hora' => $hora,
                ];
            }
        }

        return ['cuando' => 'mañana', 'hora' => $settings->horario_apertura ?? '11:00'];
    }
}