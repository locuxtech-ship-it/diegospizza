<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_negocio')->default("Diego's Pizza");
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('horario_apertura')->default('11:00');
            $table->string('horario_cierre')->default('23:00');
            $table->json('dias_laborales')->nullable();
            $table->json('tipos_servicio')->nullable();
            $table->boolean('imprimir_automaticamente')->default(false);
            $table->string('impresora_nombre')->nullable();
            $table->integer('puntos_por_dolar')->default(1);
            $table->decimal('descuento_por_punto', 8, 2)->default(2.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_settings');
    }
};
