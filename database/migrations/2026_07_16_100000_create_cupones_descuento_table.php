<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupones_descuento', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo', ['porcentaje', 'monto']);
            $table->decimal('valor', 10, 2);
            $table->decimal('monto_minimo', 10, 2)->nullable();
            $table->unsignedInteger('usos_maximos')->nullable();
            $table->unsignedInteger('usos_actuales')->default(0);
            $table->boolean('por_cliente')->default(false);
            $table->boolean('activo')->default(true);
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_expiracion')->nullable();
            $table->foreignId('cliente_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupones_descuento');
    }
};
