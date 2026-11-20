<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProducto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SeguimientoController extends Controller
{
    /**
     * Página pública de seguimiento: /seguimiento/{token}
     * Sin datos personales (ni nombre, ni teléfono, ni dirección).
     */
    public function show(string $token): Response
    {
        $pedido = $this->findByToken($token);

        $productos = PedidoProducto::with('producto')
            ->where('pedido_id', $pedido->id)
            ->get();

        return response()->view('public.seguimiento', [
            'pedido' => $pedido,
            'productos' => $productos,
            'pasos' => $this->pasos($pedido),
            'token' => $token,
        ]);
    }

    /**
     * API JSON liviana: alimenta el auto-refresh de la página (5s) y la
     * barra flotante "Mi pedido" del layout público.
     */
    public function status(string $token): JsonResponse
    {
        $pedido = $this->findByToken($token);

        return response()->json([
            'estado' => $pedido->estado,
            'numero_pedido' => $pedido->numero_pedido,
            'cancelado' => $pedido->estado === 'cancelado',
            'finalizado' => $pedido->estado === 'finalizado',
            'pasos' => $this->pasos($pedido),
            'review_url' => $pedido->estado === 'finalizado'
                ? route('review.form', ['pedido' => $pedido->id])
                : null,
            'seguimiento_url' => route('seguimiento.show', ['token' => $token]),
        ]);
    }

    private function findByToken(string $token): Pedido
    {
        abort_unless(
            preg_match('/^[a-f0-9]{32}$/', $token) === 1,
            404
        );

        $pedido = Pedido::where('seguimiento_token', $token)->first();
        abort_if(!$pedido, 404);

        return $pedido;
    }

    /**
     * Los 5 pasos del timeline con su estado y hora (Bogotá).
     * Compartido entre la vista y la API para que nunca se desincronicen.
     */
    private function pasos(Pedido $pedido): array
    {
        $defs = [
            ['key' => 'recibido', 'titulo' => 'Pedido recibido', 'icono' => '✔'],
            ['key' => 'preparacion', 'titulo' => 'En preparación', 'icono' => '👨‍🍳'],
            ['key' => 'camino', 'titulo' => 'En camino hacia tu casa', 'icono' => '🚗'],
            ['key' => 'entregado', 'titulo' => 'Entregado', 'icono' => '📍'],
            ['key' => 'finalizado', 'titulo' => 'Finalizado', 'icono' => '🎉'],
        ];

        // Índice del paso "actual" según el estado del pedido.
        $actual = match ($pedido->estado) {
            'pendiente_pago', 'pendiente' => 0,
            'en_proceso' => 1,
            'en_camino' => 2,
            'entregado' => 3,
            'finalizado' => 4,
            default => 0, // cancelado: se muestra el aviso, no el timeline
        };

        $horas = [
            'recibido' => $pedido->created_at,
            'preparacion' => null, // sin marca de tiempo propia (se infiere)
            'camino' => $pedido->en_camino_at,
            'entregado' => $pedido->entregado_at ?? $pedido->llegada_at,
            'finalizado' => $pedido->estado === 'finalizado' ? $pedido->entregado_at : null,
        ];

        $subtitulos = [
            'camino' => 'Ya salió el repartidor',
        ];

        $pasos = [];
        foreach ($defs as $i => $def) {
            $estado = $i < $actual ? 'done' : ($i === $actual ? 'current' : 'pending');

            $hora = null;
            if ($estado !== 'pending' && $horas[$def['key']]) {
                $dt = $horas[$def['key']]->copy()->setTimezone('America/Bogota');
                $hora = $dt->format('g:i') . ' ' . ((int) $dt->format('G') >= 12 ? 'p. m.' : 'a. m.');
            } elseif ($estado === 'pending') {
                $hora = 'Pendiente';
            }

            $pasos[] = [
                'key' => $def['key'],
                'titulo' => $def['titulo'],
                'icono' => $def['icono'],
                'estado' => $estado,
                'hora' => $hora,
                'subtitulo' => $estado === 'current' ? ($subtitulos[$def['key']] ?? null) : null,
            ];
        }

        return $pasos;
    }
}
