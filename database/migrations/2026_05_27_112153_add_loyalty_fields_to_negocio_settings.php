<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->decimal('puntos_ganancia_monto', 10, 2)->default(100)->after('puntos_por_dolar');
            $table->integer('puntos_ganancia_valor')->default(1)->after('puntos_ganancia_monto');
            $table->json('puntos_recompensas')->nullable()->after('puntos_ganancia_valor');
        });
    }

    public function down(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->dropColumn(['puntos_ganancia_monto', 'puntos_ganancia_valor', 'puntos_recompensas']);
        });
    }
};
