<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reataje histórico de reseñas atadas al pedido equivocado.
 *
 * Bug (arreglado en 67a5272): el form público hacía POST /review/{numero_pedido}
 * pero ReviewController::store() buscaba por id → la reseña quedaba en el pedido
 * que tuviera ese id (pedidos de junio) y guardaba el cliente de ese pedido.
 *
 * Reglas de seguridad (además de los pares verificados manualmente):
 *  - Solo actualiza si la reseña sigue en el pedido equivocado (idempotente).
 *  - Solo actualiza si la fecha (UTC) de la reseña coincide con la del destino.
 *  - Nunca toca un pedido destino que ya tenga otra reseña (0 conflictos al
 *    analizar, pero se re-verifica por si acaso).
 *  - NO toca updated_at (el orden "Fecha" del admin lo usa; no queremos que
 *    estas reseñas aparezcan como nuevas).
 *
 * Backup previo: /backups/diegospizza/pre-reataje-resenas-20261120.sql
 * Punto de restauración git: tag pre-reataje-resenas (018c3c2)
 *
 * down() revuelve a los pedidos equivocados originales (reconstruyendo el
 * cliente desde esos pedidos; para el valor byte-a-byte exacto está el dump).
 */
return new class extends Migration
{
    /** [review_id, pedido_equivocado_id, pedido_destino_id] — 20 pares de alta confianza */
    private array $pairs = [
        [61, 22, 2838],
        [60, 21, 2702],
        [59, 34, 2715],
        [58, 6, 2687],
        [57, 19, 2662],
        [56, 4, 2626],
        [55, 32, 2506],
        [54, 26, 2500],
        [53, 20, 2468],
        [51, 17, 2395],
        [49, 27, 2372],
        [48, 24, 2251],
        [47, 31, 2258],
        [46, 29, 2208],
        [45, 18, 2197],
        [44, 5, 2184],
        [43, 9, 2146],
        [42, 7, 2144],
        [41, 8, 2096],
        [29, 11, 751],
    ];

    public function up(): void
    {
        $movidas = 0;

        foreach ($this->pairs as [$reviewId, $pedidoEquivocadoId, $pedidoDestinoId]) {
            $review = DB::table('reviews')->where('id', $reviewId)->first();
            if (!$review || (int) $review->pedido_id !== $pedidoEquivocadoId) {
                continue; // ya reatajada o movimiento posterior
            }

            $destino = DB::table('pedidos')->where('id', $pedidoDestinoId)
                ->first(['id', 'cliente_id', 'created_at']);
            if (!$destino) {
                continue;
            }

            // La reseña debe ser del mismo día (UTC) que el pedido destino
            if (substr((string) $review->created_at, 0, 10) !== substr((string) $destino->created_at, 0, 10)) {
                continue;
            }

            // El destino no puede terminar con dos reseñas
            $conflicto = DB::table('reviews')
                ->where('pedido_id', $pedidoDestinoId)
                ->where('id', '!=', $reviewId)
                ->exists();
            if ($conflicto) {
                continue;
            }

            // Cliente real = cliente del pedido destino (el guardado era el del
            // pedido equivocado, porque store() tomaba $pedidoModel->cliente)
            $cliente = $destino->cliente_id
                ? DB::table('clientes')->where('id', $destino->cliente_id)->first(['nombre', 'telefono'])
                : null;

            $ok = DB::table('reviews')
                ->where('id', $reviewId)
                ->where('pedido_id', $pedidoEquivocadoId)
                ->update([
                    'pedido_id' => $pedidoDestinoId,
                    'cliente_id' => $destino->cliente_id,
                    'nombre' => $cliente->nombre ?? $review->nombre,
                    'telefono' => $cliente->telefono ?? $review->telefono,
                    // updated_at intencionalmente no se toca
                ]);

            if ($ok) {
                $movidas++;
            }
        }

        report("Reataje de resenas: {$movidas}/20 movidas a su pedido correcto");
    }

    public function down(): void
    {
        foreach ($this->pairs as [$reviewId, $pedidoEquivocadoId, $pedidoDestinoId]) {
            $review = DB::table('reviews')->where('id', $reviewId)->first();
            if (!$review || (int) $review->pedido_id !== $pedidoDestinoId) {
                continue;
            }

            $equivocado = DB::table('pedidos')->where('id', $pedidoEquivocadoId)
                ->first(['id', 'cliente_id']);
            if (!$equivocado) {
                continue;
            }

            $cliente = $equivocado->cliente_id
                ? DB::table('clientes')->where('id', $equivocado->cliente_id)->first(['nombre', 'telefono'])
                : null;

            DB::table('reviews')
                ->where('id', $reviewId)
                ->where('pedido_id', $pedidoDestinoId)
                ->update([
                    'pedido_id' => $pedidoEquivocadoId,
                    'cliente_id' => $equivocado->cliente_id,
                    'nombre' => $cliente->nombre ?? $review->nombre,
                    'telefono' => $cliente->telefono ?? $review->telefono,
                ]);
        }
    }
};
