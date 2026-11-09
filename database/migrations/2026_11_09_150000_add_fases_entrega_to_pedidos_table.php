<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->timestamp('en_camino_at')->nullable()->after('fecha_entrega');
            $table->timestamp('entregado_at')->nullable()->after('en_camino_at');
        });

        // Backfill histórico: pedidos ya finalizados -> entregado_at = fecha_entrega
        // (fecha_entrega es el momento de cierre; se usa como aproximación de llegada).
        // en_camino_at no se puede reconstruir: queda NULL y se captura desde ahora.
        DB::table('pedidos')
            ->whereNull('entregado_at')
            ->whereNotNull('fecha_entrega')
            ->update(['entregado_at' => DB::raw('fecha_entrega')]);
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['en_camino_at', 'entregado_at']);
        });
    }
};
