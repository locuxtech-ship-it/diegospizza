<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->json('metodos_pago_activos')->nullable()->after('nequi');
        });
    }

    public function down(): void
    {
        Schema::table('negocio_settings', function (Blueprint $table) {
            $table->dropColumn('metodos_pago_activos');
        });
    }
};
