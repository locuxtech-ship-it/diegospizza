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
            $table->string('seguimiento_token', 64)->nullable()->unique()->after('numero_pedido');
        });

        // Backfill: token aleatorio para los pedidos existentes (páginas de
        // seguimiento y QR del ticket también aplican a pedidos históricos).
        DB::table('pedidos')
            ->whereNull('seguimiento_token')
            ->orderBy('id')
            ->chunkById(200, function ($pedidos) {
                foreach ($pedidos as $pedido) {
                    DB::table('pedidos')
                        ->where('id', $pedido->id)
                        ->update(['seguimiento_token' => bin2hex(random_bytes(16))]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('seguimiento_token');
        });
    }
};
