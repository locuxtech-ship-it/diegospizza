<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Repartidor asignado al pedido (nullable: se puede asignar en cualquier momento)
            $table->foreignId('repartidor_id')->nullable()->after('cliente_id')->constrained('users')->nullOnDelete();
            // Marca "ya llegó" del repartidor (no cambia el estado)
            $table->timestamp('llegada_at')->nullable()->after('entregado_at');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('repartidor_id');
            $table->dropColumn('llegada_at');
        });
    }
};
