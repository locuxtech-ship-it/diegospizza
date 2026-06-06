<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('descuento_manual', 10, 2)->default(0)->after('descuento_puntos');
            $table->string('descuento_manual_tipo')->nullable()->after('descuento_manual');
            $table->decimal('descuento_manual_valor', 10, 2)->default(0)->after('descuento_manual_tipo');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['descuento_manual', 'descuento_manual_tipo', 'descuento_manual_valor']);
        });
    }
};
