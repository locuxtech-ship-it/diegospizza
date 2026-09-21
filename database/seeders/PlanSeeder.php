<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            'gratis' => [
                'nombre' => 'Gratis',
                'precio_mensual' => 0,
                'max_usuarios' => 1,
                'descripcion' => 'Para probar Facilo',
                'orden' => 1,
                'features' => [
                    'menu_digital' => true,
                    'pedidos' => ['enabled' => true, 'limits' => ['max_pedidos_mes' => 50]],
                    'punto_venta' => false,
                    'whatsapp_bot' => false,
                    'reportes' => false,
                    'fidelidad' => false,
                    'multi_usuario' => false,
                    'cierre_caja' => false,
                    'historial' => false,
                    'clientes' => false,
                    'resenas' => false,
                    'exportar_csv' => false,
                    'reporte_personalizado' => false,
                ],
            ],
            'arranque' => [
                'nombre' => 'Arranque',
                'precio_mensual' => 14900,
                'max_usuarios' => 1,
                'descripcion' => 'Para empezar a operar en serio',
                'orden' => 2,
                'features' => [
                    'menu_digital' => true,
                    'pedidos' => ['enabled' => true, 'limits' => ['max_pedidos_mes' => null]],
                    'punto_venta' => true,
                    'whatsapp_bot' => false,
                    'reportes' => ['enabled' => true, 'limits' => ['tipo' => 'basico']],
                    'fidelidad' => false,
                    'multi_usuario' => false,
                    'cierre_caja' => true,
                    'historial' => true,
                    'clientes' => true,
                    'resenas' => false,
                    'exportar_csv' => false,
                    'reporte_personalizado' => false,
                ],
            ],
            'negocio' => [
                'nombre' => 'Negocio',
                'precio_mensual' => 29900,
                'max_usuarios' => 5,
                'descripcion' => 'El plan ancla para operar bien',
                'orden' => 3,
                'features' => [
                    'menu_digital' => true,
                    'pedidos' => ['enabled' => true, 'limits' => ['max_pedidos_mes' => null]],
                    'punto_venta' => true,
                    'whatsapp_bot' => true,
                    'reportes' => ['enabled' => true, 'limits' => ['tipo' => 'avanzado']],
                    'fidelidad' => true,
                    'multi_usuario' => true,
                    'cierre_caja' => true,
                    'historial' => true,
                    'clientes' => true,
                    'resenas' => true,
                    'exportar_csv' => false,
                    'reporte_personalizado' => false,
                ],
            ],
            'pro' => [
                'nombre' => 'Pro',
                'precio_mensual' => 44900,
                'max_usuarios' => 0,
                'descripcion' => 'Todo lo que Facilo puede ofrecer',
                'orden' => 4,
                'features' => [
                    'menu_digital' => true,
                    'pedidos' => ['enabled' => true, 'limits' => ['max_pedidos_mes' => null]],
                    'punto_venta' => true,
                    'whatsapp_bot' => true,
                    'reportes' => ['enabled' => true, 'limits' => ['tipo' => 'avanzado']],
                    'fidelidad' => true,
                    'multi_usuario' => true,
                    'cierre_caja' => true,
                    'historial' => true,
                    'clientes' => true,
                    'resenas' => true,
                    'exportar_csv' => true,
                    'reporte_personalizado' => true,
                ],
            ],
        ];

        DB::connection('landlord')->transaction(function () use ($plans) {
            foreach ($plans as $slug => $data) {
                $plan = Plan::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'nombre' => $data['nombre'],
                        'precio_mensual' => $data['precio_mensual'],
                        'max_usuarios' => $data['max_usuarios'],
                        'descripcion' => $data['descripcion'],
                        'orden' => $data['orden'],
                        'activo' => true,
                    ]
                );

                foreach ($data['features'] as $key => $cfg) {
                    $enabled = is_bool($cfg) ? $cfg : $cfg['enabled'];
                    $limits = is_bool($cfg) ? null : ($cfg['limits'] ?? null);

                    PlanFeature::updateOrCreate(
                        ['plan_id' => $plan->id, 'feature_key' => $key],
                        ['enabled' => $enabled, 'limits' => $limits]
                    );
                }

                PlanFeature::where('plan_id', $plan->id)
                    ->whereNotIn('feature_key', array_keys($data['features']))
                    ->delete();
            }
        });

        $this->command?->info('Planes sembrados correctamente.');
    }
}
