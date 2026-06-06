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
    ];

    protected function casts(): array
    {
        return [
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

    public static function isOpen(): bool
    {
        $settings = self::getSettings();
        
        $now = now()->locale('es');
        $dayName = ucfirst($now->dayName);
        
        $diasLaborales = $settings->dias_laborales ?? ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        
        if (!in_array($dayName, $diasLaborales)) {
            return false;
        }
        
        $apertura = \Carbon\Carbon::parse($settings->horario_apertura ?? '11:00');
        $cierre = \Carbon\Carbon::parse($settings->horario_cierre ?? '23:00');
        $horaActual = $now->copy();
        
        if ($cierre < $apertura) {
            return $horaActual->gte($apertura) || $horaActual->lt($cierre);
        }
        
        return $horaActual->gte($apertura) && $horaActual->lt($cierre);
    }
}